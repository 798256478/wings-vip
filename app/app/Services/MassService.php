<?php

namespace App\Services;

use App\Models\Card;
use DB;
use App\Models\Mass;
use App\Models\OperatingRecord;
use App\Services\OperatingRecordService;
use App\Services\TemplateMessageService;
use App\Services\JobService;
use App\Services\NoticeService;
use App;
use TopClient;
use AlibabaAliqinFcSmsNumSendRequest;

class MassService
{
    const TOTAL_COST = '总消费';
    const AVG_CONSUME = '次均消费';
    const AVG_MONTH_CONSUME = '月均消费';
    const TOTAL_VISIT = '总到店';
    const AVG_MONTH_VISIT = '月均到店';
    const VIP_ACTIVATE = '会员卡激活';
    const TICKET_HOLD = '优惠券持有';
    const AVG_TICKET = '次均用券';
    const LAST_VISIT_TIME = '最后到店';
    const BONUS = '积分';
    const BALANCE = '余额';
    const LEVEL = '等级';
    const MONTHRECEIVE = '本月推送';

    function getMassTemplateList(){
        return Mass::get();
    }

    function getMassTemplate($id){
        return Mass::WHERE("_id", $id)->first();
    }

    function delMassTemplate($id){
        return Mass::destroy($id);
    }

    function saveMassTemplate($data){
        return Mass::insertGetId($data);
    }

    public function getSendTop()
    {
        return Card::orderBy('month_receive', 'desc')->take(100)->get();
    }

    function getQueryResult($query){
        $result = $this->queryHelper($query);
        $countSql = "select count(id) as total ".$result['sql'];
        $resultSql = "select * ".$result['sql']." limit 100";
        $cardIdList = DB::select(DB::raw("select id ".$result['sql']), $result['safe']);
        $cardIdList = array_map(function($e){
            return is_object($e) ? $e->id : $e['id'];
        }, $cardIdList);
        //判断当前发送是不是该月第一次发送
        $operatingRecordService = new OperatingRecordService;
        $massHistory = $operatingRecordService->getListByAction('营销群发');
        if(count($massHistory) > 0){
            $day1 = strtotime(date('Y-m', time()) . '-1 00:00:00');
            if($massHistory[0]->create_time < $day1) {
                Card::where('id', '>', 0)->update(['month_receive' => 0]);
            }
        }else {
            Card::where('id', '>', 0)->update(['month_receive' => 0]);
        }
        $unbaleSend = Card::select('id')->where('month_receive', '4')->whereIn('id', $cardIdList)->get()->toArray();
        $data['count'] = DB::select(DB::raw($countSql), $result['safe']);
        $data['cards'] = DB::select(DB::raw($resultSql), $result['safe']);
        $data['unable'] = count($unbaleSend);
        return $data;
    }

    function send($sendArr, $user){
        $result = $this->queryHelper($sendArr['query']);
        $sql = "select id ".$result['sql'];
        $cardList = DB::select(DB::raw($sql), $result['safe']);
        $cardList = array_map(function($e){
            return is_object($e) ? $e->id : $e['id'];
        }, $cardList);
        //执行job
        $jobService = new JobService;
        $jobMsg = $jobService->doJobs($cardList, ['type'=>'mass', 'reason'=>'营销赠送'], $sendArr['jobs']);
        $selfCards = [];
        $referrerCards = [];
        $self_message = [];
        $referrer_message = [];
        //处理job返回值
        if (isset($jobMsg['SELF'])) {
            $selfCards = array_column($jobMsg['SELF']['cards']->toArray(), 'id');
            $self_message = $jobMsg['SELF']['jobMessage'];
        }elseif (isset($jobMsg['REFERRER'])) {
            $referrerCards = array_column($jobMsg['REFERRER']['cards']->toArray(), 'id');
            $referrer_message = $jobMsg['REFERRER']['jobMessage'];
        }
        $changes = $this->getChanges($self_message, $referrer_message);
        $cardsList = array_unique(array_merge($selfCards, $referrerCards));
        //发送信息
        $totalCard = Card::count();
        if ($totalCard == count($cardsList)) {
            $cardList = 'ALL';
        }else {
            $cardList = $cardsList;
        }
        $noticeService = new NoticeService;
        $noticeService->send($cardList, $sendArr['notice']);

        //保存历史纪录
        $record = new OperatingRecord;
        $record->data = $sendArr;
        $record->operator = [
            'id' => $user->id,
            'display_name' => $user->display_name,
            'roles' => $user->roles,
        ];
        $record->cards = $cardsList;
        $record->channel = 'marketer';
        $record->show_to_member = false;
        $record->create_time = time();
        $record->action = '营销群发';
        $record->summary = '营销群发';
        $record->minimal = '营销群发';
        $record->type = 'MASS';
        $record->changes = $changes['changes'];;
        $record->mass = $changes['mass'];
        $record->save();
    }

    function getMassHistory(){
        $operatingRecordService = new OperatingRecordService;
        return $operatingRecordService->getListByAction('营销群发');
    }

    private function getChanges($self_message, $referrer_message)
    {
        $changes = [
            'changes' => [],
            'mass' => [],
        ];
        $message = array_merge($self_message, $referrer_message);
        foreach ($message as $value) {
            switch ($value['name']) {
                case 'bonus':
                    $changes['changes']['bonus'] = $value['value'];
                    $changes['mass']['bonus'] = $value['value'];
                    break;
                case 'balance':
                    $changes['changes']['balance'] = $value['value'];
                    $changes['mass']['balance'] = $value['value'];
                    break;
                case 'ticket':
                    $changes['changes']['ticket'] = $value['count'];
                    $changes['mass']['ticket'] = [
                        'count' => $value['count'],
                        'ticket' => $value['value'],
                    ];
                    break;
                case 'property':
                    $changes['changes']['property'] = $value['count'];
                    $changes['mass']['property'] = [
                        'property' => $value['value'],
                        'count' => $value['count'],
                    ];
                    break;
                case 'level':
                    $changes['changes']['level'] = $value['value'];
                    $changes['mass']['level'] = $value['value'];
                    break;
                default:
                    # code...
                    break;
            }
        }
        return $changes;
    }

    private function queryhelper($data){
        $select = "select id,card_code,nickname";
        $where = []; $order = "";
        $join = array(); $safeValue = [];
        foreach ($data as $value) {
            if(isset($value['minval']) && isset($value['maxval'])){
                if($value['minval'] != "" || $value['maxval'] != "" || $value['isshow']){
                    $str = $this->switchquery($value, $select, $where, $join, $safeValue);
                    if($str != "")
                        $join[] = $str;
                }
            }
        }
        $allWhere ="";
        if(count($where) > 0){
            $allWhere = ' where ';
            for ($i=0; $i < count($where); $i++) {
                $allWhere .= $where[$i];
                if($i + 1 < count($where)){
                    $allWhere .= " and ";
                }
            }
        }
        $select .= " from cards " . $allWhere;
        $sql = " FROM ( " . $select . ") as myc ";
        if(count($join) > 0){
            $sql .= " straight_JOIN ";
        }
        if(count($join) > 0){
            for ($i=0; $i < count($join); $i++) {
                $sql .= " (".$join[$i].") as mytb".$i;
                $sql .= " on myc.id = mytb".$i.".card_id";
                if($i+1 < count($join)){
                    $sql .= " straight_JOIN ";
                }
            }
        }
        return ['sql' => $sql, 'safe' => $safeValue];
    }

    private function switchquery($data, &$select, &$where, &$join, &$safeValue){
        switch ($data['text']) {
            case MassService::TOTAL_COST:
                if($data['isshow']){
                    $select .= ", total_expense as ". $data['text'];
                }
                if($data['minval'] != "" && $data['maxval'] != ""){
                    $safeValue['TOTAL_COST_MIN'] = $data['minval'];
                    $safeValue['TOTAL_COST_MAX'] = $data['maxval'];
                    $where[] = "total_expense >= :TOTAL_COST_MIN and total_expense < :TOTAL_COST_MAX";
                }else if($data['minval'] != ""){
                    $safeValue['TOTAL_COST_MIN'] = $data['minval'];
                    $where[] = "total_expense >= :TOTAL_COST_MIN";
                }else if($data['maxval'] != ""){
                    $safeValue['TOTAL_COST_MAX'] = $data['maxval'];
                    $where[] = "total_expense < :TOTAL_COST_MAX";
                }
                break;
            case MassService::MONTHRECEIVE:
                if($data['isshow']){
                    $select .= ", month_receive as ". $data['text'];
                }
                if($data['minval'] != "" && $data['maxval'] != ""){
                    $safeValue['MONTHRECEIVE_MIN'] = $data['minval'];
                    $safeValue['MONTHRECEIVE_MAX'] = $data['maxval'];
                    $where[] = "month_receive >= :MONTHRECEIVE_MIN and month_receive < :MONTHRECEIVE_MAX";
                }else if($data['minval'] != ""){
                    $safeValue['MONTHRECEIVE_MIN'] = $data['minval'];
                    $where[] = "month_receive >= :MONTHRECEIVE_MIN";
                }else if($data['maxval'] != ""){
                    $safeValue['MONTHRECEIVE_MAX'] = $data['maxval'];
                    $where[] = "month_receive < :MONTHRECEIVE_MAX";
                }
                break;
            case MassService::AVG_CONSUME:
                if($data['isshow']){
                    $select .= ", FORMAT(total_expense/total_visit,2) as ". $data['text'];
                }
                if($data['minval'] != "" && $data['maxval'] != ""){
                    $safeValue['AVG_CONSUME_MIN'] = $data['minval'];
                    $safeValue['AVG_CONSUME_MAX'] = $data['maxval'];
                    $where[] = "total_expense/total_visit >= :AVG_CONSUME_MIN".
                        " and total_expense/total_visit < :AVG_CONSUME_MAX";
                }else if($data['minval'] != ""){
                    $safeValue['AVG_CONSUME_MIN'] = $data['minval'];
                    $where[] = "total_expense/total_visit >= :AVG_CONSUME_MIN";
                }else if($data['maxval'] != ""){
                    $safeValue['AVG_CONSUME_MAX'] = $data['maxval'];
                    $where[] = "total_expense/total_visit < :AVG_CONSUME_MAX";
                }
                break;
            case MassService::AVG_MONTH_CONSUME:
                if($data['isshow']){
                    $select .= ", FORMAT(total_expense/(floor(DATEDIFF(now(), created_at)/30) + 1),2) as ". $data['text'];
                }
                if($data['minval'] != "" && $data['maxval'] != ""){
                    $safeValue['AVG_MONTH_CONSUME_MIN'] = $data['minval'];
                    $safeValue['AVG_MONTH_CONSUME_MAX'] = $data['maxval'];
                    $where[] = "total_expense/(floor(DATEDIFF(now(), created_at)/30) + 1) >= :AVG_MONTH_CONSUME_MIN".
                        " and total_expense/(floor(DATEDIFF(now(), created_at)/30) + 1) < :AVG_MONTH_CONSUME_MAX";
                }else if($data['minval'] != ""){
                    $safeValue['AVG_MONTH_CONSUME_MIN'] = $data['minval'];
                    $where[] = "total_expense/(floor(DATEDIFF(now(), created_at)/30) + 1) >= :AVG_MONTH_CONSUME_MIN";
                }else if($data['maxval'] != ""){
                    $safeValue['AVG_MONTH_CONSUME_MAX'] = $data['maxval'];
                    $where[] = "total_expense/(floor(DATEDIFF(now(), created_at)/30) + 1) < :AVG_MONTH_CONSUME_MAX";
                }
                break;
            case MassService::TOTAL_VISIT:
                if($data['isshow']){
                    $select .= ", total_visit as ". $data['text'];
                }
                if($data['minval'] != "" && $data['maxval'] != ""){
                    $safeValue['TOTAL_VISIT_MIN'] = $data['minval'];
                    $safeValue['TOTAL_VISIT_MAX'] = $data['maxval'];
                    $where[] = "total_visit >= :TOTAL_VISIT_MIN and total_visit < :TOTAL_VISIT_MAX";
                }else if($data['minval'] != ""){
                    $safeValue['TOTAL_VISIT_MIN'] = $data['minval'];
                    $where[] = "total_visit >= :TOTAL_VISIT_MIN";
                }else if($data['maxval'] != ""){
                    $safeValue['TOTAL_VISIT_MAX'] = $data['maxval'];
                    $where[] = "total_visit < :TOTAL_VISIT_MAX";
                }
                break;
            case MassService::AVG_MONTH_VISIT:
                if($data['isshow']){
                    $select .= ", FORMAT(total_visit/(floor(DATEDIFF(now(), created_at)/30) + 1),2) as ". $data['text'];
                }
                if($data['minval'] != "" && $data['maxval'] != ""){
                    $safeValue['AVG_MONTH_VISIT_MIN'] = $data['minval'];
                    $safeValue['AVG_MONTH_VISIT_MAX'] = $data['maxval'];
                    $where[] = "total_visit/(floor(DATEDIFF(now(), created_at)/30) + 1) >= :AVG_MONTH_VISIT_MIN".
                        " and total_visit/(floor(DATEDIFF(now(), created_at)/30) + 1) < :AVG_MONTH_VISIT_MAX";
                }else if($data['minval'] != ""){
                    $safeValue['AVG_MONTH_VISIT_MIN'] = $data['minval'];
                    $where[] = "total_visit/(floor(DATEDIFF(now(), created_at)/30) + 1) >= :AVG_MONTH_VISIT_MIN";
                }else if($data['maxval'] != ""){
                    $safeValue['AVG_MONTH_VISIT_MAX'] = $data['maxval'];
                    $where[] = "total_visit/(floor(DATEDIFF(now(), created_at)/30) + 1) < :AVG_MONTH_VISIT_MAX";
                }
                break;
            case MassService::VIP_ACTIVATE:
                if($data['isshow']){
                    $select .= ", created_at as ". $data['text'];
                }
                if($data['minval'] != "" && $data['maxval'] != ""){
                    $safeValue['VIP_ACTIVATE_MIN'] = getTimeFromW3CTime($data['minval']);
                    $safeValue['VIP_ACTIVATE_MAX'] = getTimeFromW3CTime($data['maxval']);
                    $where[] = "created_at >= :VIP_ACTIVATE_MIN and created_at < :VIP_ACTIVATE_MAX";
                }else if($data['minval'] != ""){
                    $safeValue['VIP_ACTIVATE_MIN'] = getTimeFromW3CTime($data['minval']);
                    $where[] = "created_at >= :VIP_ACTIVATE_MIN";
                }else if($data['maxval'] != ""){
                    $safeValue['VIP_ACTIVATE_MAX'] = getTimeFromW3CTime($data['maxval']);
                    $where[] = "created_at < :VIP_ACTIVATE_MAX";
                }
                break;
            case MassService::TICKET_HOLD:
                $sql = "SELECT card_id";
                if($data['isshow']){
                    $sql .= ", cont as ". $data['text'];
                }
                $sql .= " FROM (SELECT t.card_id, COUNT(t.card_id) as cont FROM tickets as t LEFT JOIN ticket_templates as p".
                    " on t.ticket_template_id = p.id ";
                $sql .= "WHERE ISNULL(t.verified_at) AND p.end_timestamp > now() GROUP BY t.card_id) AS table1 ";
                if($data['minval'] != "" && $data['maxval'] != ""){
                    $safeValue['TICKET_HOLD_MIN'] = $data['minval'];
                    $safeValue['TICKET_HOLD_MAX'] = $data['maxval'];
                    $sql .= "where cont >= :TICKET_HOLD_MIN and cont < :TICKET_HOLD_MAX order by cont desc";
                }else if($data['minval'] != ""){
                    $safeValue['TICKET_HOLD_MIN'] = $data['minval'];
                    $sql .= "where cont >= :TICKET_HOLD_MIN order by cont desc";
                }else if($data['maxval'] != ""){
                    $safeValue['TICKET_HOLD_MAX'] = $data['maxval'];
                    $sql .= "where cont < :TICKET_HOLD_MAX order by cont desc";
                }
                $join[] = $sql;
                break;
            case MassService::AVG_TICKET:
                $sql = "SELECT card_id";
                if($data['isshow']){
                    $sql .= ", FORMAT(preuse,2) as ". $data['text'];
                }
                $sql .= " FROM (SELECT id as card_id, cont/total_visit as preuse FROM ".
                    "(SELECT c.id,c.total_visit,COUNT(t.card_id) AS cont FROM cards as c LEFT JOIN tickets AS t".
                    " ON c.id = t.card_id WHERE t.verified_at is not null GROUP by t.card_id)".
                    " as table1) as table2 ";
                if($data['minval'] != "" && $data['maxval'] != ""){
                    $safeValue['AVG_TICKET_MIN'] = $data['minval'];
                    $safeValue['AVG_TICKET_MAX'] = $data['maxval'];
                    $sql .= "where preuse >= :AVG_TICKET_MIN and preuse < :AVG_TICKET_MAX order by preuse desc";
                }else if($data['minval'] != ""){
                    $safeValue['AVG_TICKET_MIN'] = $data['minval'];
                    $sql .= "where preuse >= :AVG_TICKET_MIN order by preuse desc";
                }else if($data['maxval'] != ""){
                    $safeValue['AVG_TICKET_MAX'] = $data['maxval'];
                    $sql .= "where preuse < :AVG_TICKET_MAX order by preuse desc";
                }
                $join[] = $sql;
                break;
            case MassService::LAST_VISIT_TIME:
                if($data['isshow']){
                    $select .= ", updated_at as ". $data['text'];
                }
                if($data['minval'] != "" && $data['maxval'] != ""){
                    $safeValue['LAST_VISIT_TIME_MIN'] = getTimeFromW3CTime($data['minval']);
                    $safeValue['LAST_VISIT_TIME_MAX'] = getTimeFromW3CTime($data['maxval']);
                    $where[] = "updated_at >= :LAST_VISIT_TIME_MIN and updated_at < :LAST_VISIT_TIME_MAX";
                }else if($data['minval'] != ""){
                    $safeValue['LAST_VISIT_TIME_MIN'] = getTimeFromW3CTime($data['minval']);
                    $where[] = "updated_at >= :LAST_VISIT_TIME_MIN";
                }else if($data['maxval'] != ""){
                    $safeValue['LAST_VISIT_TIME_MAX'] = getTimeFromW3CTime($data['maxval']);
                    $where[] = "updated_at < :LAST_VISIT_TIME_MAX";
                }
                break;
            case MassService::BONUS:
                if($data['isshow']){
                    $select .= ", bonus as ". $data['text'];
                }
                if($data['minval'] != "" && $data['maxval'] != ""){
                    $safeValue['BONUS_MIN'] = $data['minval'];
                    $safeValue['BONUS_MAX'] = $data['maxval'];
                    $where[] = "bonus >= :BONUS_MIN and bonus < :BONUS_MAX";
                }else if($data['minval'] != ""){
                    $safeValue['BONUS_MIN'] = $data['minval'];
                    $where[] = "bonus >= :BONUS_MIN";
                }else if($data['maxval'] != ""){
                    $safeValue['BONUS_MAX'] = $data['maxval'];
                    $where[] = "bonus < :BONUS_MAX";
                }
                break;
            case MassService::BALANCE:
                if($data['isshow']){
                    $select .= ", balance as ". $data['text'];
                }
                if($data['minval'] != "" && $data['maxval'] != ""){
                    $safeValue['BALANCE_MIN'] = $data['minval'];
                    $safeValue['BALANCE_MAX'] = $data['maxval'];
                    $where[] = "balance >= :BALANCE_MIN and balance < :BALANCE_MAX";
                }else if($data['minval'] != ""){
                    $safeValue['BALANCE_MIN'] = $data['minval'];
                    $where[] = "balance >= :BALANCE_MIN";
                }else if($data['maxval'] != ""){
                    $safeValue['BALANCE_MAX'] = $data['maxval'];
                    $where[] = "balance < :BALANCE_MAX";
                }
                break;
            case MassService::LEVEL:
                if($data['isshow']){
                    $select .= ", level as ". $data['text'];
                }
                if($data['minval'] != "" && $data['maxval'] != ""){
                    $safeValue['LEVEL_MIN'] = $data['minval'];
                    $safeValue['LEVEL_MAX'] = $data['maxval'];
                    $where[] = "level >= :LEVEL_MIN and level < :LEVEL_MAX";
                }else if($data['minval'] != ""){
                    $safeValue['LEVEL_MIN'] = $data['minval'];
                    $where[] = "level >= :LEVEL_MIN";
                }else if($data['maxval'] != ""){
                    $safeValue['LEVEL_MAX'] = $data['maxval'];
                    $where[] = "level < :LEVEL_MAX";
                }
                break;
            default:
                return "";
                break;
        }
    }
}
