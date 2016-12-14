<?php

namespace App\Services;

use App\Models\RedeemCode;
use App\Services\JobService;
use App\Models\Card;
use App\Events\CodeRedeemed;

class RedeemCodeService
{
    private $settings;

    public function __construct()
    {
        // $service = app('SettingService');
        // $this->settings = $service->get('TEMPLATE_MESSAGES');
    }

    public function redeem($code, $card)
    {
        $redeemCode = RedeemCode::where('codes', $code)->first();

        //检查兑换码是否可用
        if (!isset($redeemCode)) {
            return [
                'status' => 'NOT_FIND',
                'title' => '没有找到',
                'message' => '兑换码错误或已失效',
            ];
        }

        if (!$redeemCode->is_many) {
            if ($redeemCode->stock_quantity == 0) {
                return [
                    'status' => 'NOT_STOCK',
                    'title' => $redeemCode->title,
                    'message' => '抱歉，您来晚了，兑换名额已满.',
                ];
            }

            if ($redeemCode->begin_timestamp > time()) {
                return [
                    'status' => 'NOT_START',
                    'title' => $redeemCode->title,
                    'message' => '抱歉，兑换活动尚未开启.',
                ];
            }
            if ($redeemCode->end_timestamp < time()) {
                return [
                    'status' => 'ENDED',
                    'title' => $redeemCode->title,
                    'message' =>'抱歉，兑换活动已结束.',
                ];
            }

            if (isset($redeemCode->records)) {
                foreach ($redeemCode->records as $record) {
                    if ($record['card_id'] === $card->id) {
                        return [
                            'status' => 'OBTAINED',
                            'title' => $redeemCode->title,
                            'message' => '抱歉，不能重复进行兑换.',
                        ];
                    }
                }
            }

        } else {
            $index = array_search($code, $redeemCode->codes);

            $codes = $redeemCode->codes;
            unset($codes[$index]);
            $redeemCode->codes = array_values($codes);

        }

        $redeemCode->stock_quantity -= 1;
        $redeemCode->redeemed_quantity += 1;

        //执行操作
        $jobService = new JobService();
        $jobResult = $jobService->doJobs([$card->id], [
            'type' => 'code',
            'reason' => '兑换'.$redeemCode->title,
        ], $redeemCode->jobs);

        //添加兑换记录
        $record = [
            'card_id' => $card->id,
            'redeem_time' => time(),
        ];
        if ($redeemCode->is_many) {
            $record['code'] = $code;
        }

        $records = $redeemCode->records;
        if (empty($records)) {
            $records = [];
        }

        $records[] = $record;

        $redeemCode->records = $records;

        //修改已兑换数量并保存
        //$redeemCode->redeemed_quantity += 1;//朱贝鸽修改上面已经有一句
        $redeemCode->save();

        //返回成功消息
        $message = '';
        //$message = var_export($jobResult,true);

        if (isset($jobResult['SELF']) && isset($jobResult['SELF']['jobMessage'])) {
            $jobMessages = $jobResult['SELF']['jobMessage'];
            foreach ($jobMessages as $jobMessage) {
                $message .= $jobMessage['tag'].':';
                $message .= $jobMessage['value'];
                if (isset($jobMessage['count'])) {
                    $message .= ' * '.$jobMessage['count'];
                }
                $message .= "\n";
            }
        }
        event(new CodeRedeemed($card,$jobResult, $code));//朱贝鸽
        
        //交易记录
        return [
            'status' => 'SUCCEED',
            'title' => $redeemCode->title,
            'message' => $message,
        ];
    }

    public function getRedeemCodeList()
    {
        return RedeemCode::orderBy('updated_at', 'desc')->get();
    }

    public function getRedeemCode($id)
    {
        $redeemCode = RedeemCode::find($id);
        if (isset($redeemCode['records'])) {
            $records = $redeemCode['records'];
            foreach ($records as &$record) {
                if (isset($record['card_id'])) {
                    $record['card'] = Card::find($record['card_id']);
                }
            }
            $redeemCode['records'] = $records;
        }
        return $redeemCode;
    }

    public function addRedeemCode($redeemCode)
    {
        $RedeemCode = new RedeemCode();
        $RedeemCode->title = $redeemCode['title'];
        $RedeemCode->is_many = $redeemCode['is_many'];
        $RedeemCode->jobs = $redeemCode['jobs'];
        $RedeemCode->redeemed_quantity = 0;
        $RedeemCode->stock_quantity = $redeemCode['stock_quantity'];
        if (!$RedeemCode->is_many) {
            $RedeemCode->begin_timestamp = $redeemCode['begin_timestamp'];
            $RedeemCode->end_timestamp = $redeemCode['end_timestamp'];
            $RedeemCode->codes = $redeemCode['codes'];
        }
        $RedeemCode->save();
        if($RedeemCode->is_many){
            $this->generationCode($RedeemCode->_id, $RedeemCode->stock_quantity);
        }

        return $RedeemCode->_id;
    }

    public function updateRedeemCode($redeemCode, $id)
    {
        $RedeemCode = RedeemCode::where('_id', $id)->first();
        if ($RedeemCode->title != $redeemCode['title']) {
            $RedeemCode->title = $redeemCode['title'];
        }
        if ($RedeemCode->jobs != $redeemCode['jobs']) {
            $RedeemCode->jobs = $redeemCode['jobs'];
        }
        if (!$RedeemCode->is_many) {
            if ($RedeemCode->codes != $redeemCode['codes']) {
                $RedeemCode->codes = $redeemCode['codes'];
            }
            if ($RedeemCode->stock_quantity != $redeemCode['stock_quantity']) {
                $RedeemCode->stock_quantity = $redeemCode['stock_quantity'];
            }
            if ($RedeemCode->begin_timestamp != $redeemCode['begin_timestamp']) {
                $RedeemCode->begin_timestamp = $redeemCode['begin_timestamp'];
            }
            if ($RedeemCode->end_timestamp != $redeemCode['end_timestamp']) {
                $RedeemCode->end_timestamp = $redeemCode['end_timestamp'];
            }
        }
        $RedeemCode->save();
    }

    public function deleteRedeemCode($redeemCodeId)
    {
        RedeemCode::destroy($redeemCodeId);
    }

    public function generationCode($redeemCodeId, $amount)
    {
        $codes = [];
        $count = 0;
        while ($count < $amount){
            $code = '';
            for ( $i = 0; $i < 8; $i++){
                $code .= chr(rand(97,122));
            }
            if (RedeemCode::where('codes', $code)->count() == 0){
                $codes[] = $code;
                $count++;
            }
        }
        $redeemCode = RedeemCode::find($redeemCodeId);
        if(isset($redeemCode->codes)){
            $redeemCode->codes = array_merge($redeemCode->codes, $codes);
        }else {
            $redeemCode->codes = $codes;
        }
        $redeemCode->stock_quantity = count($redeemCode->codes);
        $redeemCode->save();

        return $codes;
    }

    public function addRecord($record)
    {
        # 可以使用push方法把数据填到数组中
        # ->push('records', ['card_id' => 1, 'redeem_time' => 123456789]);
    }

    public function getHistoryList()
    {
        $timeList = [];
        $year = (int)date('Y', time());
        $month = (int)date('m', time());
        for ($i=0; $i < 12; $i++) {
            if ($month > 0) {
                $timeList[] = $year . '-' . $month;
                $month--;
            }else {
                $year--;
                $month = 12;
                $timeList[] = $year . '-' . $month;
            }
        }
        $typeList = [
            '按兑换码',
            '按类型'
        ];
        return ['date' => $timeList, 'type' => $typeList];
    }

    public function getRedeemCodeHistory($date, $type)
    {
        $begintime = strtotime($date . '-1 00:00:00');
        $endtime = strtotime('+1 month', $begintime);
        $day = ($endtime - $begintime) / (3600 * 24);
        $catearr = [];
        for ($i = 1; $i <= $day; ++$i) {
            $arr['begintime'] = $begintime;
            $begintime = strtotime('+1 day', $begintime);
            $arr['endtime'] = $begintime - 1;
            $arr['time'] = date('Y年m月d日', $arr['begintime']);
            $arr['day'] = $i;
            $catearr[] = $arr;
        }
        $date = strtotime("-1 year", strtotime(date('Y-m', time()).'-1'));
        $redeemCodeTmp = RedeemCode::orderBy('updated_at', 'desc')->get()->toArray();

        if ($type == '按类型') {
            $redeemCodeList = [
                'share' => [],
                'one' => [],
            ];
            foreach ($redeemCodeTmp as $redeemCode) {
                if (strtotime($redeemCode['updated_at']) > $date) {
                    $list = $this->getRecordList($redeemCode);
                    if ($redeemCode['is_many']) {
                        $redeemCodeList['share'] = array_merge($redeemCodeList['share'], $list);
                    }else {
                        $redeemCodeList['one'] = array_merge($redeemCodeList['one'], $list);
                    }
                }
            }
            $sheetData = [
                'catearr' => [],
                'table' => [
                    [
                        'name' => '共享码',
                        'data' => [],
                    ],
                    [
                        'name' => '一对一码',
                        'data' => [],
                    ],
                ],
                'type' => [
                    'style' => 'smooth',
                    'type' => 'line',
                ],
                'all' => [],
            ];
            foreach ($catearr as $cate) {
                $sheetData['catearr'][] = $cate['day'];
                $shareRecord = [];
                $oneRecord = [];
                foreach ($redeemCodeList['share'] as $record) {
                    if ($record['redeem_time'] >= $cate['begintime'] && $record['redeem_time'] <= $cate['endtime']) {
                        $shareRecord[] = $record;
                    }
                }
                $sheetData['table'][0]['data'][] = count($shareRecord);
                $sheetData['all'] = array_merge($sheetData['all'], $shareRecord);
                foreach ($redeemCodeList['one'] as $record) {
                    if ($record['redeem_time'] >= $cate['begintime'] && $record['redeem_time'] <= $cate['endtime']) {
                        $oneRecord[] = $record;
                    }
                }
                $sheetData['table'][1]['data'][] = count($oneRecord);
                $sheetData['all'] = array_merge($sheetData['all'], $oneRecord);
            }
            return $sheetData;
        }elseif ($type == '按兑换码') {
            $redeemCodeList = [];
            foreach ($redeemCodeTmp as $redeemCode) {
                if (strtotime($redeemCode['updated_at']) > $date) {
                    $redeemCodeList[] = [
                        'title' => $redeemCode['title'],
                        'list' => $this->getRecordList($redeemCode),
                    ];
                }
            }
            $sheetData = [
                'catearr' => [],
                'table' => [],
                'type' => [
                    'style' => 'smooth',
                    'type' => 'line',
                ],
                'all' => [],
            ];
            foreach ($redeemCodeList as $redeemCode) {
                $dataArr = [];
                foreach ($catearr as $cate) {
                    if (count($sheetData['catearr']) < count($catearr)) {
                        $sheetData['catearr'][] = $cate['day'];
                    }
                    $dataRecordList = [];
                    foreach ($redeemCode['list'] as $record) {
                        if ($record['redeem_time'] >= $cate['begintime'] && $record['redeem_time'] <= $cate['endtime']) {
                            $dataRecordList[] = $record;
                        }
                    }
                    $dataArr[] = count($dataRecordList);
                }
                $sheetData['table'][] = [
                    'name' => $redeemCode['title'],
                    'data' => $dataArr,
                ];
                $sheetData['all'] = array_merge($sheetData['all'], $redeemCode['list']);
            }
            return $sheetData;
        }
    }

    private function getRecordList($redeemCode)
    {
        $recordList = [];
        if (isset($redeemCode['records']) && count($redeemCode['records']) > 0) {
            foreach ($redeemCode['records'] as $record) {
                $tmp = [
                    'redeemCodeName' => $redeemCode['title'],
                    'redeem_time' => $record['redeem_time'],
                    'card' => Card::where('id', $record['card_id'])->first(),
                    'date' => date('Y-m-d H:i:s', $record['redeem_time']),
                ];
                if ($redeemCode['is_many']) {
                    $tmp['code'] = $record['code'];
                    $tmp['type'] = '一对一码';
                }else {
                    $tmp['code'] = $redeemCode['codes'][0];
                    $tmp['type'] = '分享码';
                }
                $recordList[] = $tmp;
            }
        }
        return $recordList;
    }
}
