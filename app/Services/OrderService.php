<?php

namespace App\Services;

use App;
use App\Models\OrderDetail;
use App\Models\OrderPayment;
use App\Models\Order;
use App\Models\Card;
use App\Models\commodity;
use App\Models\Ticket;
use App\Models\Refund;
use DB;

use App\Exceptions\WingException;
use App\Events\OrderCompleted;
use App\Models\Yuda\OrderSyncFail;
use App\Services\Yuda\SyncService;


class OrderService
{
    //创建储值订单
    public function createBalanceOrder($data, $channel, $card)
    {
        try {
            //事务开始
            DB::beginTransaction();
            $result = $this->doCreateBalanceOrder($data, $channel, $card);
            
            //历史记录
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
//            return json_exception_response($e);
        }
    }

    // 参数说明
    // $data = [
    //     'total_fee' => 0,
    //     'cashier_id' => 5,
    //     'payments' => [
    //         ['type' => 'WECHAT','name' => '微信线上', 'amount' => 100],
    //     ]
    // ];
    public function createConsumeOrder($data, $card)
    {
        try {
            //事务开始
            DB::beginTransaction();
            $result = $this->doCreateConsumeOrder($data, $card);
            
            //历史记录
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
//            return json_exception_response($e);
        }
    }

    // 参数说明
    // $data = [
    //     'items' => [
    //         ['specificationId' => 1, 'quantity' => 1],
    //         ['specificationId' => 2, 'quantity' => 2],
    //     ],
    //     'address_id' => 3,  (可空)
    //     'remark' => '备注', (可空)
    //     'ticket_id' => 2,   (可空)
    //     'payments' => [     (可空)
    //          ['type' => 'WECHAT','name' => '微信线上', 'amount' => 100],
    //     ]
    // ];
    public function createGoodsOrder($data, $channel, $card)
    {
        try {
            //事务开始
            DB::beginTransaction();
            $result = $this->doCreateGoodsOrder($data, $channel, $card);
            
            //历史记录
            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
            //return json_exception_response($e);
        }
    }

    //购买商品的时候 单独付款
    public function orderPayment($id)
    {
        try {
            //事务开始
            DB::beginTransaction();
            $result = $this->doOrderPayment(Order::find($id));
            //历史记录
            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
//            return json_exception_response($e);
        }
    }
    
    public function orderFulfillment($id){
        try {
            //事务开始
            DB::beginTransaction();
            $result = $this->doOrderFulfillment(Order::find($id));
            //历史记录
            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    
    public function orderComplete($id)
    {
        try {
            //事务开始
            DB::beginTransaction();
            $result = $this->doOrderComplete(Order::find($id));
            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    
    //订单关闭, 置回库存
    public function orderClose($id)
    {
        try {
            //事务开始
            DB::beginTransaction();
            $result = $this->doOrderClose(Order::find($id));
            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }


    // $data = [
    //     'balance_fee' => 0,
    //     'balance_present' => 0,
    //     'bonus_require' => 100,
    //     'cashier_id' => 5,
    //     'payments' => [
    //         ['type' => 'WECHAT','name' => '微信线上', 'amount' => 100],
    //     ]
    // ];
    public function doCreateBalanceOrder($data, $channel, $card)
    {
        $results = []; 
        $settingService = App::make('SettingService');

        $balancefee = $data['balance_fee'];

        //计算赠送额
        $balancePresent = 0;
        if ($channel == 'WECHAT') {
            $balance_rule = $settingService->get('BALANCE');
            foreach ($balance_rule['buy'] as $key => $value) {
                if ($balancefee >= $key) {
                    if ($value > 1) {
                        $balancePresent = $value;
                    }
                    else {
                        $balancePresent = $balancefee * $value;
                    }
                }
            }
        }
        else {
            $balancePresent = array_key_exists('balance_present', $data) ? $data['balance_present'] : 0;
        }
        

        //订单主表
        $order = new Order();
        $order->card_id = $card->id;
        $order->number = md5(time().mt_rand(0, 1000));
        $order->guid = getGuid();
        $order->body = '储值' . $balancefee . '元';
        $order->channel = $channel;
        $order->type = 'BALANCE';
        $order->balance_fee = $balancefee;
        $order->balance_present = $balancePresent;
        $order->total_fee = $order->balance_fee;
        $order->money_pay_amount = $order->balance_fee;
        $order->bonus_present = 0;
        $order->bonus_require = 0;
        $order->bonus_pay_amount = 0;
        $order->state = $channel === 'WECHAT' ? 'NOT_PAY' : 'FINISH';
        $order->is_need_delivery = false;

        if (array_key_exists('remark', $data) && $data['remark']) {
            $order->remark = $data['remark'];
        }
        if (array_key_exists('cashier_id', $data)) {
            $order->cashier_id = $data['cashier_id'];
        }



        if ($balancePresent && $balancePresent != 0) {
            $order->body .= '，赠送' . $balancePresent . '元';
        }

        $order->save();

        //判断是否积分换储值
        if (array_key_exists('bonus_require', $data) && $data['bonus_require'] !== 0) {
            $order->bonus_require = $data['bonus_require'];
            return $this->doOrderPayment($order);
        }

        //是否在线支付
        if ($channel === 'WECHAT') {
            $paymentSetting = $settingService->get('PAYMENT');
            $methods = $paymentSetting['methods'];

            $order_payment = new OrderPayment();
            $order_payment->order_id = $order->id;
            $order_payment->type = 'WECHAT';
            $order_payment->name = '微信线上';
            foreach ($methods as $value){
                if ($value['type'] === 'WECHAT')
                    $order_payment->name = $value['name'];
            }
            $order_payment->amount = $balancefee;
            $order_payment->save();

            //在线储值,微信以分为单位,储值额乘100
            $money = $order->money_pay_amount*100;

            return $this->WechatPaymentPrepare($order, $card, $money);
        }
        elseif ($channel === 'SHOP') {
            if (array_key_exists('payments', $data) && count($data['payments']) > 0) {
                
                $orderPayments = $this->buildOrderPayments($data['payments']);
                $moneyPayAmount = 0;
                foreach ($orderPayments as $payment) {
                    $moneyPayAmount += $payment->amount;
                }

                //验证支付合计与订单实际支付额是否相等
                if ($order->money_pay_amount != $moneyPayAmount){
                    throw new WingException('订单支付方式信息异常。', 401);//TODO:优化
                }

                //计算积分
                if ($order->bonus_require === 0){
                    $order->bonus_present = $this->calculateBonus($orderPayments, $order->type);
                    $order->save();
                }

                //添加支付信息
                if ($orderPayments != null) {
                    $order->orderPayments()->saveMany($orderPayments);
                }

            }
            else {
                throw new WingException('订单支付方式信息异常。', 401);
            }
            return $this->doOrderPayment($order);
        }
    }

    // $data = [
    //     'total_fee' => 0,
    //     'cashier_id' => 5,
    //     'bonus_present'=>10,积分赠送,可空,空值时使用配置项计算赠送积分
    //     'guid'=>'aasd'裕达的guid,可空,有值时使用值,空值时生成guid
    //     'payments' => [
    //         ['type' => 'WECHAT','name' => '微信线上', 'amount' => 100],
    //     ]
    // ];
    public function doCreateConsumeOrder($data, $card)
    {
        $orderPayments = null;
        $bonus_present = 0;
        if (array_key_exists('payments', $data) && count($data['payments']) > 0) {

            $orderPayments = $this->buildOrderPayments($data['payments'],$data['total_fee']);
            $moneyPayAmount = 0;
            foreach ($orderPayments as $payment) {
                $moneyPayAmount += $payment->amount;
            }

            //验证支付合计与订单实际支付额是否相等
            if ($data['total_fee'] != $moneyPayAmount) {
                throw new WingException('订单支付方式信息异常。', 401);//TODO:优化
            }

            if(isset($data['bonus_present'])){
                $bonus_present = $data['bonus_present'];
            }else {
                $bonus_present = $this->calculateBonus($orderPayments, 'CONSUME');
            }
        }
        else {
            throw new WingException('订单支付方式信息异常。', 401);
        }

        $order = new Order();
        $order->card_id = $card->id;
        $order->number = md5(time().mt_rand(0, 1000));
        $order->guid = isset($data['guid']) ? $data['guid'] : getGuid();
        $order->body = '店内消费';
        $order->channel = 'SHOP';
        $order->type = 'CONSUME';
        $order->balance_fee = 0;
        $order->balance_present = 0;
        $order->total_fee = $moneyPayAmount;
        $order->money_pay_amount = $moneyPayAmount;
        $order->bonus_present = $bonus_present;
        $order->bonus_require = 0;
        $order->bonus_pay_amount = 0;
        $order->state = 'FINISH';
        $order->is_need_delivery = false;

        if (array_key_exists('remark', $data) && $data['remark']) {
            $order->remark = $data['remark'];
        }
        if (array_key_exists('cashier_id', $data)) {
            $order->cashier_id = $data['cashier_id'];
        }


        $order->save();

        //保存支付信息
        if ($orderPayments != null) {
            $order->orderPayments()->saveMany($orderPayments);
        }

        return $this->doOrderPayment($order);
    }

    // 参数说明
    // $data = [
    //     'items' => [
    //         ['specificationId' => 1, 'quantity' => 1],
    //         ['specificationId' => 2, 'quantity' => 2],
    //     ],
    //     'address_id' => 3,  (可空)
    //     'remark' => '备注', (可空)
    //     'ticket_id' => 2,   (可空)
    //     'cashier_price_deductions' => 100,(可空)
    //     'cashier_bonus_deductions' => 200,(可空)
    //     'cashier_id' -> 5,
    //     'payments' => [     (可空)
    //          ['type' => 'WECHAT','name' => '微信线上', 'amount' => 100],
    //     ]
    // ];
    public function doCreateGoodsOrder($data, $channel, $card)
    {
        $results = [];
        $orderDetails = $this->buildGoodsOrderDetails($data);
        $order = $this->buildGoodsOrder($data, $orderDetails, $channel, $card);

        //是否存在金额手动减免
        if (array_key_exists('cashier_price_deductions', $data)) {
            $order->cashier_price_deductions = $data['cashier_price_deductions'];
            $order->money_pay_amount -= $order->cashier_price_deductions;
        }

        //是否存在积分手动减免
        if (array_key_exists('cashier_bonus_deductions', $data)) {
            $order->cashier_bonus_deductions = $data['cashier_bonus_deductions'];
            $order->bonus_require -= $order->cashier_bonus_deductions;
        }

        if (array_key_exists('cashier_id', $data)) {
            $order->cashier_id = $data['cashier_id'];
        }

        $orderPayments = null;
        if (array_key_exists('payments', $data) && count($data['payments']) > 0) {
            
            $orderPayments = $this->buildOrderPayments($data['payments'],$order->money_pay_amount);
            $moneyPayAmount = 0;
            foreach ($orderPayments as $payment) {
                $moneyPayAmount += $payment->amount;
            }

            //验证支付合计与订单实际支付额是否相等
            if ($order->money_pay_amount != $moneyPayAmount){
                throw new WingException('订单支付方式信息异常。', 401);//TODO:优化
            }

            //计算积分
            if ($order->bonus_require === 0){
                $order->bonus_present = $this->calculateBonus($orderPayments, $order->type);
            }
        }else{
            if ($order->money_pay_amount != 0){
                throw new WingException('订单支付方式信息异常。', 401);//TODO:优化
            }
        }

        //验证收货地址
        if ($order->is_need_delivery && !$order->address) {
            throw new WingException('收货地址未填写。', 401);
        }
        //TODO:优化，是否需要其他验证？
        $order->save();
        
        //优惠券核销
        if ($order->ticket_id) {
            $ticketService = new TicketService();
            $ticketService->writeoffTicketNotEvent($order->ticket_id);
        }

        foreach ($orderDetails as $detail) {
            $specification = $detail->commoditySpecificationHistory->commoditySpecification;
            if ($detail->amount > $specification->stock_quantity) {
                throw new WingException('商品数量不足', 401);
            }
            $specification->stock_quantity = $specification->stock_quantity - $detail->amount;
            $specification->save();
        }

        //添加订单明细
        $order->orderDetails()->saveMany($orderDetails);

        //添加支付信息
        if ($orderPayments != null) {
            $order->orderPayments()->saveMany($orderPayments);
        }

        //是否在线支付
        if ($channel === 'WECHAT') {
            $hasWechatPay = false;
            $money = 0;
            if($orderPayments != null){
                foreach ($orderPayments as $payment) {
                    if ($payment->type === "WECHAT") {
                        $hasWechatPay = true;
                        $money = $payment->amount * 100;
                    }
                }
            }
            if ($hasWechatPay) {
                $order->state = "NOT_PAY";
                $results['pay_config'] = $this->WechatPaymentPrepare($order, $card, $money);
            }
            else {
                $order = $this->doOrderPayment($order);
            }
        }
        elseif ($channel === 'SHOP') {
            $order = $this->doOrderPayment($order);
        }
        else {
            //TODO:
            throw new WingException('订单支付方式信息异常。', 401);
        }

        $results['state'] = $order->state;
        $results['order_id'] = $order->id;
        return $results;
    }

    //付款完成
    private function doOrderPayment($order)
    {
        //进行余额与积分减扣
        $balance = 0;
        $bonus = 0;
        foreach ($order->orderPayments as $model) {
            if ($model->type == 'BALANCE') {
                if ($balance === 0) {
                    $balance = $model->amount;
                }
                else {
                    throw new WingException('余额付款重复', 401);
                }
            }
            if ($model->type == 'BONUS') {
                if ($bonus === 0) {
                    $bonus = $model->use_bonus;
                }
                else {
                    throw new WingException('积分付款重复', 401);
                }
            }
        }
        $bonus += $order->bonus_pay_amount;
        if ($bonus !== 0 || $balance !== 0) {

            $card = Card::find($order->card_id);

            if ($balance !== 0) {
                if ($balance > $card->balance) {
                    throw new WingException('余额不足', 401);
                }
                $card->balance = $card->balance - $balance;
            }

            //card减去积分
            if ($bonus !== 0) {
                if($bonus > $card->bonus) {
                    throw new WingException("积分不足", 401);
                }
                $card->bonus = $card->bonus - $bonus;
            }

            $card->save();
        }

        //更改order状态。付款类型金额。
        $order->state = 'PAY_SUCCESS';//Q:这里考虑再进行一步验证
        $order->pay_time =  date('y-m-d H:i:s',time());
        $order->save();

        return $this->doOrderFulfillment($order);
    }
    
    private function doOrderFulfillment($order)
    {
        if ($order->type == 'BALANCE'){
            //修改余额
            $card = Card::find($order->card_id);
            $card->balance = $card->balance + $order->balance_fee + $order->balance_present;
            $card->save();
//            return $card;

        } 
        elseif ($order->type == 'GOODS'){
            //遍历订单商品清单，依次处理
            $card = Card::find($order->card_id);
            foreach ($order->orderDetails as $model) {
                if($model->commoditySpecificationHistory->is_suite){
                    foreach($model->commoditySpecificationHistory->suiteChildHistories as $suiteChild){
                        $this->tryAddTicketOrProperty($card, $model->amount * $suiteChild->pivot->count, $suiteChild);
                    }
                }else{
                    $this->tryAddTicketOrProperty($card, $model->amount, $model->commoditySpecificationHistory);
                }
            }
        } 
        // else {} 消费无需执行

        //如无需配送直接进入下一环节
        if ($order->is_need_delivery) {
            //TODO:这里应该有个状态变更
            return $order;
        }
        else {
            return $this->doOrderComplete($order);
        }
    }
    
    //订单完成
    private function doOrderComplete($order)
    {
        //更新会员卡消费统计
        $card = Card::find($order->card_id);

        //计算积分：
        //储值根据规则赠送。消费赠送金额直接是页面上填写的字段。
        //赠送积分。购买如果需要积分的话是促销商品，不再赠送积分。
        $card->bonus += $order->bonus_present;

        if ($order->type != 'BALANCE') {
            $card->total_expense += $order->money_pay_amount;
            $card->total_visit += 1;
        }

        $card->save();

        //修改订单状态，更新数据库
        $order->state = 'FINISH';
        $order->finish_time = date('y-m-d H:i:s',time());
        $order->save();
        //触发事件 有错，凭掉
        event(new OrderCompleted($card, $order));

        //同步至裕达
        if(user_domain() === config('yuda.name') && $order->channel === 'WECHAT'){
            $this->sendOrderData($order,$card);
        }

        return $order;
    }
    
    private function doOrderClose($order)
    {
        //修改订单状态
        $order->state = 'CLOSED';
        $order->close_time = date('y-m-d H:i:s',time());
        $order->save();

        if($order->ticket_id){
            $ticket=Ticket::find($order->ticket_id);
            $ticket->verified_at=null;
            $ticket->save();
        }

        foreach ($order->orderDetails as $detail) {
             $commoditySpecification = $detail->commoditySpecification;
             $commoditySpecification->stock_quantity += $detail->amount;
             $commoditySpecification->save();
        }
    }

    public function buildGoodsOrderDetails($data)
    {
        $itemIds = [];
        foreach ($data['items'] as $item) {
            $itemIds[] = $item['specificationId'];
        }

        $CommodityService = new CommodityService();
        $specificationsHistory = $CommodityService->getCommoditySpecificationsHistoryLastByArray($itemIds);

        $details = [];
        foreach ($specificationsHistory as $specification) {
            $quantity = 0;
            foreach ($data['items'] as $item) {
                if ($specification->commodity_specification_id == $item['specificationId'])
                    $quantity = $item['quantity'];
            }

            $detail = new OrderDetail();
            $detail->commoditySpecificationHistory()->associate($specification);
            $detail->commoditySpecification()->associate($specification->commoditySpecification);
            $detail->unit_price = $specification->price;
            $detail->unit_bonus_require = $specification->bonus_require;
            $detail->amount = $quantity;
            $detail->total_price = $specification->price * $quantity;
            $detail->total_bonus_require = $specification->bonus_require * $quantity;

            $details[] = $detail;
        }

        return $details;
    }

    public function buildGoodsOrder($data, $details, $channel, $card)
    {
        $order = new Order();
        $order->card_id = $card->id;
        $order->number = md5(time().mt_rand(0, 1000));
        $order->guid = getGuid();
        $order->channel = $channel;
        $order->type = 'GOODS';

        $total_fee = 0;
        $bonus_require = 0;
        $body = '商城购买:';
        $is_need_delivery = false;
        foreach ($details as $detail) {
            $specification = $detail->commoditySpecificationHistory;
            if ($specification->is_need_delivery) {
                $is_need_delivery = true;
            }
            $body .= $specification->commodityHistory->name . $specification->name;
            $total_fee += $detail->total_price;
            $bonus_require += $detail->total_bonus_require;
        }
        $order->total_fee = $total_fee;
        $order->bonus_require = $bonus_require;
        $order->bonus_pay_amount = $bonus_require;
        $order->body = $body;
        $order->is_need_delivery = $is_need_delivery;
        $order->bonus_present = 0;//TODO:处理积分赠送

        if ($is_need_delivery) {
            $addressService = new AddressService();
            if (array_key_exists('address_id', $data) && $data['address_id']) {
                $order->address = $addressService->getAddressStringByCardIdAndAddressId($card->id,$data['address_id']);
            }
            else {
                $order->address = $addressService->getDefaultAddressStringByCardId($card->id);
            }
        }

        if (array_key_exists('remark', $data) && $data['remark']) {
            $order->remark = $data['remark'];
        }

        if (array_key_exists('ticket_id', $data)) {
            $ticketService = new TicketService();
            $ticket = $ticketService->getTicket($data['ticket_id']);
            if($ticket) {
                $order->money_pay_amount = $order->total_fee - $ticketService->calculateOffByTicket($ticket, $order->total_fee);
                $order->ticket_id = $data['ticket_id'];
            }
            else {
                $order->money_pay_amount = $order->total_fee;
            }
            
            
        }
        else {
            $order->money_pay_amount = $order->total_fee;
        }

        $order->state = 'NOT_PAY';

        return $order;
    }

    private function buildOrderPayments($payments,$orderPrice = null)
    {
        $orderPayments = [];

        foreach ($payments as $payment){
            if ((isset($payment['amount']) && $payment['amount'] != 0) ||
                (isset($payment['use_bonus']) && $payment['use_bonus'] != 0)) {
                $orderPayment = new OrderPayment();
                $orderPayment->type = $payment['type'];
                if($orderPayment->type === 'BONUS'){
                    $bonusRule = $this->getBonusRule();
                    $max = ($orderPrice * $bonusRule['use'] > $bonusRule['limit']) ? $bonusRule['limit'] :
                        $orderPrice * $bonusRule['use'];
                    $max = round($max,2);
                    if($max < $payment['use_bonus'] * $bonusRule['exchange']){
                        throw new WingException('订单支付方式信息异常。', 401);
                    }
                    $amount = $payment['use_bonus'] * $bonusRule['exchange'];
                    $amount = round($amount,2);
                    $orderPayment->use_bonus = $payment['use_bonus'];
                    $orderPayment->amount = $amount;
                }
                else{
                    $orderPayment->amount = $payment['amount'];

                }
                if (array_key_exists('name', $payment))
                    $orderPayment->name = $payment['name'];
                else
                    $orderPayment->name = $this->getPaymentNameByType($payment['type']);

                $orderPayments[] = $orderPayment;
            }
        }

        return $orderPayments;
    }

    private function getPaymentNameByType($type)
    {
        $settingService = App::make('SettingService');
        $paymentMethods = $settingService->get('PAYMENT')['methods'];
        foreach ($paymentMethods as $method) {
            if ($method['type'] == $type) {
                return $method['name'];
            }
        }
        //TODO:抛出异常
    }

    private function WechatPaymentPrepare($order, $card, $money)
    {
        $wechatOrder = new \EasyWeChat\Payment\Order([
            'trade_type'       => 'JSAPI', 
            'body'             => $order->body,
            'detail'           => $order->body,
            'out_trade_no'     => $order->number,
            'total_fee'        => $money,
            'openid'           => $card->openid,
            //'notify_url'       => 'http://xxx.com/order-notify', 
        ]);

        $wechat = App::make('WechatApplication');
        $payment = $wechat->payment;
        $result = $payment->prepare($wechatOrder);

        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            return $payment->configForJSSDKPayment($result->prepay_id);
        }
        else {
            return new \App\Exceptions\WingException('支付请求失败，请重试。');
        }
    }
    
    //分别在券，服务表里面添加资产
    private function tryAddTicketOrProperty($card,$amount,$commoditySpecificationHistory)
    {
        if ($commoditySpecificationHistory->sellable_type === "App\Models\TicketTemplate") {
            for ($i = 0; $i < $amount; ++$i) {
                $ticket = new  Ticket();
                $ticket->ticket_code = substr(time(), 2).rand(1000, 9999);
                $ticket->card_id = $card->id;
                $ticket->ticket_template_id = $commoditySpecificationHistory->sellable_id;
                $ticket->is_wechat_received = false;
                $ticket->verified_at = null;
                $ticket->save();
            }
        } 
        elseif ($commoditySpecificationHistory->sellable_type === "App\Models\PropertyTemplate") {
            $propertyService = new PropertyService;
            $propertyService->AddProperty(
                $card->id,
                $commoditySpecificationHistory->sellable_id,
                $commoditySpecificationHistory->sellable_quantity * $amount,
                $commoditySpecificationHistory->sellable_validity_days * $amount
            );
        }
    }


    public function assembleSql($option)
    {
        $option['page']=(int)$option['page'];
        if($option['page']>0) {
            $query = Order::with('card', 'orderDetails.commoditySpecificationHistory.commodityHistory','orderDetails.refund', 'orderPayments');
            if (isset($option['state']) && !empty($option['state'] && $option['state']!='ALL')) {
                $query = $query->where('state', $option['state']);
            }
            if (isset($option['type']) && !empty($option['type'] && $option['type']!='ALL')) {
                $query = $query->where('type', $option['type']);
            }
            if (isset($option['channel']) && !empty($option['channel'] && $option['channel']!='ALL')) {
                $query = $query->where('channel', $option['channel']);
            }
            if (isset($option['number']) && !empty($option['number'])) {
                $query = $query->where('number','like','%'.$option['number'].'%');
            }
            if (isset($option['time']['start']) && !empty($option['time']['start'])) {
                $query = $query->where('created_at', '>', $option['time']['start']);
            }
            if (isset($option['time']['finish']) && !empty($option['time']['finish'])) {
                $query = $query->where('created_at', '<',  date("Y-m-d",strtotime("+1 day",strtotime
                ($option['time']['finish']))));
            }
            if(isset($option['showClose']) && !empty($option['showClose']) && $option['showClose']){
                $query=$query->where('state','!=',$option['showClose']);
            }
            return $query;
        }else{
            throw new \Dingo\Api\Exception\StoreResourceFailedException('页码错误', ['page' => ['页码必须大于0']]);
        }

    }

    public function getOrderData($option)
    {
        $length=6;
        $query=$this->assembleSql($option);
        $data['count']=$query->count();
        $data['order']=$query->orderBy('created_at', 'desc')->skip($length * ($option['page'] - 1))->take($length)->get();
        return $data;
    }

    public function getDashboardOrderData(){
        $length=10;
        $data['deal']= Order::with('card', 'orderDetails.commoditySpecificationHistory.commodityHistory')->whereIn('state',array('DELIVERED','FINISH','CLOSED'))->orWhere('is_need_delivery', false)->orderBy('created_at', 'desc')->skip(0)->take($length)->get();
        $data['undeal']=Order::with('card', 'orderDetails.commoditySpecificationHistory.commodityHistory')->whereIn('state',array('PAY_SUCCESS','NOT_PAY'))->where('is_need_delivery', true)->orderBy('created_at', 'desc')->skip(0)->take($length)->get();
        return $data;
    }



    public function getOrder($id)
    {
        return Order::with('card', 'orderDetails.commoditySpecificationHistory.commodityHistory', 'orderDetails.refund',
            'orderPayments','ticket.ticketTemplate', 'orderDetails.commoditySpecificationHistory.suiteChildHistories.commodityHistory')
            ->with(['ticket'=>function($query){
                $query->withTrashed();
            }])
            ->where('id',$id)->first();
    }

    public function getOrderByNumber($number)
    {
//        return Order::with('card', 'orderDetails.goodSpecification.good', 'orderDetails.refund', 'orderPayments', 'ticket')->where('number',$number)->first();
        return Order::where('number',$number)->first();
    }

    public function getRecordByOrderId($id)
    {
        $id=(int)$id;
        return App\Models\OperatingRecord::where('data.order.id',$id)->first();
    }

    //根据ID和订单类型查询记录
    public function getOrderListByIdAndType($id,$type)
    {
        return Order::where('card_id',$id)->where('type',$type)->orderBy('created_at', 'desc')->take(30)->get();
    }

     //根据卡ID查询记录
    public function getOrderListByCardId($id)
    {
        $data['orders']=Order::with(
            'orderDetails.commoditySpecificationHistory.commodityHistory',
            'refund',
            'orderDetails.refund', 
            'orderDetails.commoditySpecificationHistory.suiteChildHistories.commodityHistory'
            )->where('card_id', $id)->where('type', 'GOODS')->orderBy('created_at', 'desc')->get()->toArray();
        $data['refunds']=Refund::with(
            'order.orderDetails.commoditySpecificationHistory.commodityHistory',
            'order.orderDetails.commoditySpecificationHistory.suiteChildHistories.commodityHistory',
            'orderDetail.commoditySpecificationHistory.commodityHistory',
            'orderDetail.commoditySpecificationHistory.suiteChildHistories.commodityHistory',
            'order.card'
            )->orderBy('created_at', 'desc')->whereHas('order.card',function($q) use ($id){
            return $q->where('id', $id);
        })->get()->toArray();

        foreach ($data['refunds'] as $key => $value) {
            $refunds = Array();  
            if($value['type'] != "ORDER"){
                array_push($refunds, $value['order_detail']);
                $data['refunds'][$key]['order']['order_details'] = $refunds;
            }
        }

        //dd($data['refunds']);

        return $data;
    }

    public function getOrderDetail($id){
        return OrderDetail::with('order.card')->find($id);
    }

    public function getCardByOrderId($id){
        return Order::with('card')->find($id);
    }

    public function updateOrderDetail($order_id, $order_detail_id){
        if($order_detail_id){
            $orderDetail=$this->getOrderDetail($order_detail_id);
            $orderDetail->is_refund=true;
            $orderDetail->save();
        } else {
            $orderDetails = OrderDetail::where('order_id', $order_id)->get();
            foreach ($orderDetails as $key => $value) {
                $orderDetail = $this->getOrderDetail($value->id);
                $orderDetail->is_refund=true;
                $orderDetail->save();
            }
        }
    }

    //发货
    public function deliver($data){
        foreach($data['id'] as $k=>$v){
            $order=Order::find($v);
            $order->express_type=$data['type'];
            $order->express_code=$data['express_code'];
            $order->express_company=$data['company'];
            $order->state="DELIVERED";
            $order->delivery_time=date('Y-m-d H:i:s',time());
            $order->save();
        }
        return $order->id;
    }

    public function editAddress($data){
            $order=Order::find($data['id']);
            $order->address=$data['address'];
            $order->save();
            return $order->id;
    }

    public function getSaleTop(){
        $saleTop=OrderDetail::with('commoditySpecification')->select(DB::raw("commodity_specification_id,sum(amount) as sum,sum(total_price) as total_price"))->groupBy('commodity_specification_id')->orderBy('sum','desc')->get();
        $commodityData=commodity::get();
        foreach ($commodityData as $key => $val) {
            foreach ($saleTop as $k => $v) {
                if($val['id']==$v['commoditySpecification']['commodity_id']){
                    $val['sum']=$v['sum'];
                    $val['total_price']=$v['total_price'];
                }
            }
        }
        return $commodityData;
    }

    public function getOrderAndPayments($cardId,$id)
    {
        return Order::with('orderPayments')->where('card_id',$cardId)->where('id',$id)->first();
    }

    public function calculateBonus($payments, $orderType) {
        $settingservice = App::make('SettingService');
        $setting = $settingservice->get('PAYMENT')[strtolower($orderType)];//大小写敏感

        $bonusPresent = 0;
        foreach ($payments as $payment) {
            if (isset($setting['methods'][$payment->name])){
                $bonusPresent += $setting['methods'][$payment->name] * $payment->amount * $setting['bonus'];
            }
            else {
                $bonusPresent += $payment->amount * $setting['bonus'];
            }
        }
        return $bonusPresent;
    }

    public function getBonusRule()
    {
        $settingService = App::make('SettingService');
        $bonusRule = $settingService->get('PAYMENT')['bonus_rule'];
        return $bonusRule;

    }

//    微信会员系统订单同步至裕达
//    同步两次失败后记录并通知管理员
    private function sendOrderData($order,$card)
    {
        $data = [
            'type'=>'ORDER',
            'timestamp'=>time(),
            'data'=>[
                'id'=>$order->guid,
                'code'=>$card->card_code,
                'summary'=>$order->body,
                'total_fee'=>$order->money_pay_amount,//实付金额
                'bonus_require'=>$order->bonus_pay_amount,//实付积分
                'bonus_present'=>$order->bonus_present,//赠送积分
            ]
        ];
        $syncService = new SyncService();
        $res = $syncService->sendDatatoYuda($data);
        if(!isset($res['status']) ||  $res['status'] !== 'SUCCEED'){
            $res = $syncService->sendDatatoYuda($data);
            if(!isset($res['status']) ||  $res['status'] !== 'SUCCEED'){
//                TODO:记录订单同步失败
                $orderSynvFail = new OrderSyncFail();
                $orderSynvFail->order_id = $order->id;
                $orderSynvFail->card_id = $card->id;
                $orderSynvFail->time = date('Y-m-d H:i:s',$data['timestamp']);
                $orderSynvFail->state = 'FAIL';
                $orderSynvFail->back_data = $res['back_data'];
                $orderSynvFail->send_data = $res['send_data'];
                $orderSynvFail->error_code = $res['error_code'];
                $orderSynvFail->http_code = $res['http_code'];
                $orderSynvFail->save();
//                TODO 通知管理员

            }
        }
    }

    //yuda订单同步至微信会员系统
    public function yudaOrderSyncToThis($param)
    {
        $rules = [
            'id' => 'required | string',
            'code' => 'required | integer',
            'summary' => 'required | string',
            'total_fee' => 'required | numeric',
            'bonus_require' => 'required | integer',
            'bonus_present' => 'required | integer',
        ];
        $validator = app('validator')->make($param['data'], $rules);
        if($validator->fails()){
            return $validator->errors()->all();
        }

        $cardService = new CardService();
        $card = $cardService->getCardByCode($param['data']['code']);
        if(!$card){
            return '会员不存在';
        }
        $data = [];
        $data['channel'] = 'SHOP';
        $data['total_fee'] = $param['data']['total_fee'];
        $data['bonus_present'] = $param['data']['bonus_present'];
        $data['guid'] = $param['data']['id'];
        $data['payments'] = [
            [
                'name'=>$this->getPaymentNameByType('CASHIER'),
                'type'=>'CASHIER',
                'amount'=>$param['data']['total_fee']
            ]
        ];
        try {
            $res = $this->createConsumeOrder($data,$card);
        } catch (\Exception $e) {
            $message = method_exists($e, 'getMessage') ? $e->getMessage() : 'could_not_login';
            $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
            $errors = method_exists($e, 'getErrors') ? $e->getErrors() : [];
            $arr = [
                'message' => $message,
                'status_code' => $statusCode,
                'errors' => $errors,
            ];
            return $arr;
//            return json_exception_response($e);
        }
        return true;
    }
}
