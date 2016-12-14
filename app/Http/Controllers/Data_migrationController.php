<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use DB;
use MongoClient;
use Hash;
use App\Models\OperatingRecord;
use App\Models\Commodity;
use App\Models\CommoditySpecification;
use App\Models\CommoditySuiteChild;
use App\Models\CommodityHistory;
use App\Models\CommoditySpecificationHistory;
use App\Models\CommoditySuiteChildHistory;

class Data_migrationController extends BaseController
{
    function index(){
        $MD = new MongoClient();
        $olddb = $MD->selectDB('alayna');
        $cdk= $olddb->operation_log;
        // $olddb = $MD->selectDB('wings-vip');
        // $cdk= $olddb->operating_records;
        $cdkarr = iterator_to_array($cdk->find(['channel' => '收银端'])->sort(['create_time' => 1]), false);
        // $cdkarr = iterator_to_array($cdk->aggregate(['channel' => 1], ['items' => []], [])->sort(['create_time' => -1]), false);
        var_dump($cdkarr);
    }

    public function basic_table(){
        $newusers = DB::connection('mysql')->table('users')->get();
        if(count($newusers) > 0){
            echo "already build!";
            return;
        }
        //users
        $oldusers = DB::connection('mysql2')->table('user')->get();
        $newusers = array();
        foreach ($oldusers as $user) {
            $userarr = array(
                'display_name' => $user->user_name,
                'login_name' => $user->staffid,
                'roles' => $user->role,
                'password' => Hash::make("111111"),
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time())
            );
            $newusers[] = $userarr;
        }
        DB::connection('mysql')->table('users')->insert($newusers);

        //ticket_templates
        $olds = DB::connection('mysql2')->table('ticket_type')->get();
        $tickettmparr = array();
        foreach ($olds as $old) {
            if ($old->end_timestamp > time()) {
                $old->wechat_ticket_id = $old->ticket_type_card_id;
                unset($old->ticket_type_card_id);
                unset($old->fixed_term);
                unset($old->fixed_begin_term);
                $old->created_at = date('Y-m-d H:i:s', time());
                $old->updated_at = date('Y-m-d H:i:s', time());
                $old->begin_timestamp = date('Y-m-d H:i:s', $old->begin_timestamp);
                $old->end_timestamp = date('Y-m-d H:i:s', $old->end_timestamp);
                $tickettmparr[] = get_object_vars($old);
            }
        }
        DB::connection('mysql')->table('ticket_templates')->insert($tickettmparr);

        //cards
        $olds = DB::connection('mysql2')->table('card')->get();
        $cardarr = [];
        $openidList = [];
        foreach ($olds as &$old) {
            if (!in_array($old->openid, $openidList)) {
                $old->pin = $old->word;
                unset($old->word);
                $old->created_at = date('Y-m-d H:i:s', $old->activation_time);
                unset($old->activation_time);
                $old->is_wechat_received = $old->wechat_geted;
                unset($old->wechat_geted);
                $old->updated_at = date('Y-m-d H:i:s', time());
                unset($old->init_bonus);
                unset($old->init_balance);
                $cardarr[] = get_object_vars($old);
                $openidList[] = $old->openid;
            }
        }
        DB::connection('mysql')->table('cards')->insert($cardarr);

        //tickets
        $olds = DB::connection('mysql2')->table('ticket')->get();
        $ticketarr = array();
        foreach ($olds as $old) {
            $card = DB::connection('mysql')->table('cards')
                            ->where('card_code', $old->card_code)->first();
            if($card == null){
                continue;
            }
            $old->card_id = $card->id;
            unset($old->card_code);
            $ticket_tmp = DB::connection('mysql')->table('ticket_templates')
                            ->where('wechat_ticket_id', $old->ticket_type_card_id)->first();
            if($ticket_tmp == null){
                continue;
            }
            $old->ticket_template_id = $ticket_tmp->id;
            unset($old->ticket_type_card_id);
            $old->created_at = date('Y-m-d H:i:s', $old->create_time);
            unset($old->create_time);
            $old->updated_at = date('Y-m-d H:i:s', $old->get_time);
            unset($old->get_time);
            if($old->consume_time != null){
                $old->deleted_at = date('Y-m-d H:i:s', $old->consume_time);
                $old->verified_at = date('Y-m-d H:i:s', $old->consume_time);
            }else{
                $old->deleted_at = null;
                $old->verified_at = null;
            }
            unset($old->consume_time);
            $ticketarr[] = get_object_vars($old);
        }
        DB::connection('mysql')->table('tickets')->insert($ticketarr);

        //商品
        $olds = DB::connection('mysql2')->table('bonus_gift')->get();
        foreach ($olds as $val) {
            if($val->type == 'ticket'){
                $oldticket = DB::connection('mysql2')->table('ticket_type')
                                ->where('ticket_type_card_id', $val->ticket_type_card_id)->first();
                if($oldticket == null){
                    continue;
                }
                $newticket = DB::connection('mysql')->table('ticket_templates')
                                ->where('wechat_ticket_id', $oldticket->ticket_type_card_id)->first();
                $good_ticket = array(
                    'name' => $oldticket->title,
                    'summary' => '副标题',
                    'code' => 123456,
                    'is_on_offer' => $val->is_disable,
                    'disable_coupon' => 0,
                    'is_single_specification' => 1,
                    'bonus_require' => $val->consume_points,
                    'image' => '["\/upload\/wings-vip\/7341BD22D5D64A29928C2F9D358F9070.jpg"]',
                    'detail' => '',
                    'created_at' => date('Y-m-d H:i:s', time()),
                    'updated_at' => date('Y-m-d H:i:s', time())
                );
                $goodId = DB::connection('mysql')->table('commodities')->insertGetId($good_ticket);
                $good_specification = array(
                    'commodity_id' => $goodid,
                    'bonus_require' => $val->consume_points,
                    'sellable_type' => 'App\Models\TicketTemplate',
                    'sellable_id' => $newticket->id,
                    'name' => '',
                    'stock_quantity' => 1000,
                    'full_name' => $oldticket->title,
                    'is_on_offer' => $val->is_disable,
                    'is_suite' => 0,
                    'is_need_delivery' => 0,
                    'created_at' => date('Y-m-d H:i:s', time()),
                    'updated_at' => date('Y-m-d H:i:s', time())
                );
                DB::connection('mysql')->table('commodity_specifications')->insert($good_specification);
                $this->createHistory($goodId);
            }elseif($val->type == 'good'){
                $good_product = array(
                    'name' => $val->good_name,
                    'summary' => '副标题',
                    'code' => 123456,
                    'is_on_offer' => $val->is_disable,
                    'is_single_specification' => 1,
                    'disable_coupon' => 0,
                    'bonus_require' => $val->consume_points,
                    'image' => '["\/upload\/wings-vip\/7341BD22D5D64A29928C2F9D358F9070.jpg"]',
                    'detail' => '',
                    'created_at' => date('Y-m-d H:i:s', time()),
                    'updated_at' => date('Y-m-d H:i:s', time())
                );
                $goodId = DB::connection('mysql')->table('commodities')->insertGetId($good_product);
                $good_specification = array(
                    'commodity_id' => $goodId,
                    'bonus_require' => $val->consume_points,
                    'name' => '',
                    'full_name' => $val->good_name,
                    'stock_quantity' => 1000,
                    'is_on_offer' => $val->is_disable,
                    'is_suite' => 0,
                    'is_need_delivery' => 0,
                    'created_at' => date('Y-m-d H:i:s', time()),
                    'updated_at' => date('Y-m-d H:i:s', time())
                );
                DB::connection('mysql')->table('commodity_specifications')->insert($good_specification);
                $this->createHistory($goodId);
            }elseif($val->type == 'value'){

            }
        }

        $MD = new MongoClient();
        $olddb = $MD->selectDB('alayna');
        $oldoperation_log= $olddb->operation_log;
        //order
        $order_consume = iterator_to_array($oldoperation_log->find(array('action' => '消费')), false);
        if(count($order_consume) > 0){
            foreach ($order_consume as $consume) {
                $olduser = DB::connection('mysql2')->table('user')->where('id', $consume['operator']['id'])->first();
                $newuser = DB::connection('mysql')->table('users')->where('login_name', $olduser->staffid)->first();
                foreach ($consume['card_codes'] as $card) {
                    $card_arr = DB::connection('mysql')->table('cards')->where('card_code', $card)->first();
                    $conarr = array(
                        'card_id' => $card_arr->id,
                        'number' => str_replace('-', '', substr(com_create_guid(), 1, 36)),
                        'body' => "店内消费",
                        'remark' => '',
                        'type' => 'CONSUME',
                        'channel' => 'SHOP',
                        'total_fee' => $consume['consume'],
                        'bonus_present' => $consume['bonus'],
                        'is_need_delivery' => 0,
                        'state' => 'FINISH',
                        'pay_time' => date('Y-m-d H:i:s', $consume['create_time']),
                        'finish_time' => date('Y-m-d H:i:s', $consume['create_time']),
                        'cashier_id' => $newuser->id,
                        'created_at' => date('Y-m-d H:i:s', $consume['create_time']),
                        'updated_at' => date('Y-m-d H:i:s', $consume['create_time'])
                    );
                    $order_payments_arr = array();
                    if($consume['balance'] != 0 && $consume['consume'] === abs($consume['balance'])){
                        $orderid = DB::connection('mysql')->table('orders')->insertGetId($conarr);
                        $op = array(
                            'order_id' => $orderid,
                            'name' => '现金',
                            'type' => 'CASHIER',
                            'amount' => abs($consume['balance']),
                            'created_at' => date('Y-m-d H:i:s', $consume['create_time']),
                            'updated_at' => date('Y-m-d H:i:s', $consume['create_time'])
                        );
                        DB::connection('mysql')->table('order_payments')->insert($op);
                    }else if($consume['balance'] != 0 && $consume['consume'] > abs($consume['balance'])){
                        $conarr['money_pay_amount'] = $consume['consume'] - abs($consume['balance']);
                        $orderid = DB::connection('mysql')->table('orders')->insertGetId($conarr);
                        $ops = array(
                            array(
                                'order_id' => $orderid,
                                'name' => '现金',
                                'type' => 'CASHIER',
                                'amount' => abs($consume['balance']),
                                'created_at' => date('Y-m-d H:i:s', $consume['create_time']),
                                'updated_at' => date('Y-m-d H:i:s', $consume['create_time'])
                            ),
                            array(
                                'order_id' => $orderid,
                                'name' => '余额',
                                'type' => 'BALANCE',
                                'amount' => $conarr['money_pay_amount'],
                                'created_at' => date('Y-m-d H:i:s', $consume['create_time']),
                                'updated_at' => date('Y-m-d H:i:s', $consume['create_time'])
                            )
                        );
                        DB::connection('mysql')->table('order_payments')->insert($ops);
                    }else{
                        $conarr['money_pay_amount'] = $consume['consume'];
                        $orderid = DB::connection('mysql')->table('orders')->insertGetId($conarr);
                        $op = array(
                            'order_id' => $orderid,
                            'name' => '余额',
                            'type' => 'BALANCE',
                            'amount' => $conarr['money_pay_amount'],
                            'created_at' => date('Y-m-d H:i:s', $consume['create_time']),
                            'updated_at' => date('Y-m-d H:i:s', $consume['create_time'])
                        );
                        DB::connection('mysql')->table('order_payments')->insert($op);
                    }
                }
            }
        }
        //chuzhi
        $order_consume = iterator_to_array($oldoperation_log->find(array('action' => '储值')), false);
        if(count($order_consume) > 0){
            foreach ($order_consume as $consume) {
                $olduser = DB::connection('mysql2')->table('user')->where('id', $consume['operator']['id'])->first();
                $newuser = DB::connection('mysql')->table('users')->where('login_name', $olduser->staffid)->first();
                foreach ($consume['card_codes'] as $card) {
                    $card_arr = DB::connection('mysql')->table('cards')->where('card_code', $card)->first();
                    $conarr = array(
                        'card_id' => $card_arr->id,
                        'number' => str_replace('-', '', substr(com_create_guid(), 1, 36)),
                        'body' => "店内储值",
                        'remark' => '',
                        'type' => 'BALANCE',
                        'channel' => 'SHOP',
                        'total_fee' => $consume['balance'],
                        'bonus_present' => 0,
                        'balance_fee' => $consume['balance'],
                        'is_need_delivery' => 0,
                        'state' => 'FINISH',
                        'pay_time' => date('Y-m-d H:i:s', $consume['create_time']),
                        'finish_time' => date('Y-m-d H:i:s', $consume['create_time']),
                        'cashier_id' => $newuser->id,
                        'created_at' => date('Y-m-d H:i:s', $consume['create_time']),
                        'updated_at' => date('Y-m-d H:i:s', $consume['create_time'])
                    );
                    $order_payments_arr = array();
                    if($consume['balance']){
                        $orderid = DB::connection('mysql')->table('orders')->insertGetId($conarr);
                        $op = array(
                            'order_id' => $orderid,
                            'name' => '现金',
                            'type' => 'CASHIER',
                            'amount' => abs($consume['balance']),
                            'created_at' => date('Y-m-d H:i:s', $consume['create_time']),
                            'updated_at' => date('Y-m-d H:i:s', $consume['create_time'])
                        );
                        DB::connection('mysql')->table('order_payments')->insert($op);
                    }
                }
            }
        }

        //登录
        $order_consume = iterator_to_array($oldoperation_log->find(array('action' => '登录')), false);
        if(count($order_consume) > 0){
            foreach ($order_consume as $consume) {
                $newuser = DB::connection('mysql')->table('users')->where('login_name', $consume['operator']['staffid'])->first();
                $conarr = array(
                    'user_id' => $newuser->id,
                    'login_name' => $newuser->login_name,
                    'display_name' => $newuser->display_name,
                    'roles' => $newuser->roles,
                    'created_at' => date('Y-m-d H:i:s', $consume['create_time']),
                    'updated_at' => date('Y-m-d H:i:s', $consume['create_time'])
                );
                $orderid = DB::connection('mysql')->table('login_records')->insertGetId($conarr);
            }
        }

        //records
        $order_consume = iterator_to_array($oldoperation_log->find(array('action' => 'auto')), false);
        if(count($order_consume) > 0){
            foreach ($order_consume as $consume) {
                $newuser = DB::connection('mysql')->table('users')->where('login_name', $consume['operator']['staffid'])->first();
                $log = [
                    'operator' => null,
                    'cards' => $consume['card_codes'],
                    'channel' => 'wechat',
                    'show_to_menber' => TRUE,
                    'create_time' => date('Y-m-d H:i:s', $consume['create_time']),
                    'action' => '获得奖励',
                    'data'=> $consume['data'],
                    'summary' => $consume['summary'],
                    'minimal' => $consume['minimal'],
                    'type'=>'event',
                    'event_class'=>'app/events/orderCompleted',//事件类别,可空
                    'card'=>[],//card表中所有字段,可空
                    'changes'=>[],
                ];
                if (isset($consume['data']['actions']['send_ticket'])) {
                    $ticket = DB::connection('mysql')->table('ticket_templates')
                    ->where('wechat_ticket_id', $consume['data']['actions']['send_ticket']['ticket_type_card_id'])->first();
                    $log['changes']['ticket'] = $ticket->id;
                }
                if ($consume['data']['name'] == '新会员') {
                    $log['event_type'] = 'CARD_CREATE';
                }elseif ($consume['data']['name'] == '会员填写资料') {
                    $log['event_type'] = 'CARD_EVENT';
                }
                $mongodb = $MD->selectDB('wings-vip');
                $records = $olddb->operating_records;
                $records->insert($log);
            }
        }

        //redeemcode
        $olddb = $MD->selectDB('alayna');
        $cdkey= $olddb->CDkey;
        $keyHistory = $olddb->CDkey_history;
        $cdkeyList = iterator_to_array($cdkey->find(), false);
        foreach ($cdkeyList as $key) {
            $ticket = DB::connection('mysql')->table('ticket_templates')
                            ->where('wechat_ticket_id', $key['ticket_type_card_id'])->first();
            if($ticket == null){
                continue;
            }
            $newKey = [
                'begin_timestamp' => strtotime($key['begin_timestamp']),
                'end_timestamp' => strtotime($key['end_timestamp']),
                'codes' => [$key['code']],
                'is_many' => false,
                'jobs' => [
                    'args' => ['ticketTemplateId' => $ticket->id,'count' => 1],
                    'class' => 'App\Jobs\GiveTicket',
                    'recipient' => 'SELF',
                ],
                'redeemed_quantity' => 11,
                'stock_quantity' => $key['num'],
                'title' => $key['title'],
                'created_at' => 11,
                'updated_at' => 11,
            ];
            $historyList = iterator_to_array($keyHistory->find(['CDkeyid' => $key['id']]), false);
            $cardIdList = [];
            foreach ($historyList as $val) {
                $card = DB::connection('mysql')->table('cards')->where('card_code', $val['card_code'])->first();
                $cardIdList[] = [
                    'card_id' => $card->id,
                    'redeem_time' => time(),
                ];
            }
            $newKey['records'] = $cardIdList;
            $mongodb = $MD->selectDB('wings-vip');
            $records = $olddb->operating_records;
            $records->insert($log);
        }

        echo "build success!";
    }

    private function createHistory($commodityId)
    {
        $commodity = Commodity::where('id', $commodityId)->with('specifications')->first();
        $commodityHistory = new CommodityHistory();
        $commodityHistory->commodity_id = $commodity->id;
        $commodityHistory->name = $commodity->name;
        $commodityHistory->summary = $commodity->summary;
        $commodityHistory->code = $commodity->code;
        $commodityHistory->image = $commodity->image;
        $commodityHistory->detail = $commodity->detail;
        $commodityHistory->price = $commodity->price;
        $commodityHistory->bonus_require = $commodity->bonus_require;
        $commodityHistory->is_single_specification = $commodity->is_single_specification;
        $commodityHistory->disable_coupon = $commodity->disable_coupon;
        $commodityHistory->save();
        foreach ($commodity->specifications as $specification) {
            $spec = new CommoditySpecificationHistory();
            $spec->commodity_history_id = $commodityHistory->id;
            $spec->commodity_specification_id = $specification->id;
            $spec->name = $specification->name;
            $spec->full_name = $specification->full_name;
            $spec->price = $specification->price;
            $spec->bonus_require = $specification->bonus_require;
            $spec->sellable_type = $specification->sellable_type;
            $spec->sellable_id = $specification->sellable_id;
            $spec->sellable_quantity = $specification->sellable_quantity;
            $spec->sellable_validity_days = $specification->sellable_validity_days;
            $spec->is_suite = $specification->is_suite;
            $spec->is_need_delivery = $specification->is_need_delivery;
            $commodityHistory->specificationHistories()->save($spec);
        }
        if ($commodity->specifications[0]->is_suite) {
            $commodityHistorySuits = [];
            $suiteChildren = CommoditySuiteChild::where('suite_id', $commodity->specifications[0]->id)
                ->with('commoditySpecifications')->get();
            foreach ($suiteChildren as $suite) {
                $specificationHistory = CommoditySpecificationHistory::where('commodity_specification_id', $suite->commoditySpecifications->id)
                    ->orderBy('id', 'desc')->first();
                $suit = [
                    'suite_history_id' => $commodityHistory->specificationHistories[0]->id,
                    'child_history_id' => $specificationHistory->id,
                    'count' => $suite->count,
                ];
                $commodityHistorySuits[] = $suit;
            }
            CommoditySuiteChildHistory::insert($commodityHistorySuits);
        }
    }
}
