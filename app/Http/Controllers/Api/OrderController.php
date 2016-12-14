<?php

namespace App\Http\Controllers\Api;

use App\Services\CommodityService;
use Dingo\Api\Http\Request;
use App\Exceptions\WingException;
use App\Services\AuthService;

use App\Services\OrderService;
use App\Services\SettingService;
use App\Models\Card;

class OrderController extends Controller
{
    protected $authService;
    protected $orderService;

    public function __construct(AuthService $authService, OrderService $orderService)
    {
        $this->authService = $authService;
        $this->orderService = $orderService;
    }
    
    public function createGoodsOrder(Request $request)
    {
        try {
          app('validator')->make($request->all(), [
                'channel'=>['require'],
                'cardid'=>['int'],
                'data.payments' => ['array'],
                'data.payments.*.name' => ['require'],
                'data.payments.*.type' => ['require'],
                'data.payments.*.amount' => ['Numeric'],
                'data.items' =>['array'],
                // 'data.items.*.specificationId' =>['int'],
                // 'data.items.*.quantity' =>['int'],
                'data.cashier_id' =>['require'],
                'data.bonus_require' => ['require'],
                'data.cashier_price_deductions' => ['require'],
                'data.cashier_bonus_deductions' => ['require'],
            ]);
            $data = $request->input('data');
            $channel=$request->input('channel');
            $card=Card::find($request->input('cardid'));
            $result = $this->orderService->createGoodsOrder($data,$channel,$card);
            if (!($result === true)) {
                return $result;
            }
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }
    
    public  function createBalanceOrder(Request $request)
    {
        try {
          app('validator')->make($request->all(), [
                'channel'=>['require'],
                'cardid'=>['int'],
                'data.cashier_id' =>['require'],
                'data.balance_fee' => ['Numeric'],
            ]);
            $data = $request->input('data');
            $channel=$request->input('channel');
            $card=Card::find($request->input('cardid'));
            $result = $this->orderService->createBalanceOrder($data,$channel,$card);
            if (!($result === true)) {
                return $result;
            }
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }
    
    
    public  function createConsumeOrder(Request $request)
    {
       try {
          app('validator')->make($request->all(), [
                'channel'=>['require'],
                'cardid'=>['int'],
                'data.payments' => ['array'],
                'data.payments.*.name' => ['require'],
                'data.payments.*.type' => ['require'],
                'data.payments.*.amount' => ['Numeric'],
                'data.cashier_id' =>['require'],
                'data.total_fee' => ['Numeric'],
            ]);
            $data = $request->input('data');
            $channel=$request->input('channel');
            $card=Card::find($request->input('cardid'));
            $result = $this->orderService->createConsumeOrder($data,$card);
            if (!($result === true)) {
                return $result;
            }
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }
    
    

    public function paySuccess()
    {
        try {
            $orderId = $request->input('orderId');
            $result = $this->orderService->paySuccessTransaction($orderId);
            if (!($result === true)) {
                return $result;
            }
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }
    public function finish(Request $request)
    {
        try {
            $orderId = $request->input('orderId');
            $result = $this->orderService->finish($orderId);
            if (!($result === true)) {
                return $result;
            }
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }
    public function close(Request $request)
    {
        try {
            $orderId = $request->input('orderId');
            $result = $this->orderService->orderClose($orderId);
            if (!($result === true)) {
                return $result;
            }
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }


    //验证订单
    private function checkorder(Request $request)
    {
        $rules = [
            'card_id' => ['required', 'integer'],
            // 'number'=>['required','max:32'],
            'body' => ['required', 'max:32'],
            // 'remark'=>['required'],
            'type' => ['required', 'in:CONSUME,BALANCE,GOODS'],
            'channel' => ['required', 'in:SHOP,WECHAT,DELIVERY'],
            // 'total_fee'=>['required'],
            // 'bonus_require'=>['required', 'integer','max:11'],
            // 'bonus_present'=>['required', 'integer','max:11'],
            // 'money_pay_amount'=>['required','numeric'],
            // 'balance_pay_amount'=>['required','numeric'],
            // 'state'=>['required','in:NOTPAY,PAY_SUCCESS,FINISH,CLOSED']
        ];
        $order = $request->input('order');
        $validator = app('validator')->make($order, $rules);
        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('订单验证失败', $validator->errors());
        }

        return  $order;
    }



    //验证支付方式
    private function checkPayment($model)
    {
        $rules = [
            // 'order_id' => ['required', 'integer','max:10'],
            'type' => ['required'],
            'name'=> ['required'],
            'amount' => ['required', 'numeric'],
        ];
        $validator = app('validator')->make($model, $rules);
        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('支付方式验证失败', $validator->errors());
        }

        return  $model;
    }

    public function getOrderData($option){
        $option=json_decode($option,true);
        return $this->orderService->getOrderData($option);
    }

    /*public function getOrderTotal($opt){
        return $this->orderService->getOrderTotal($opt);
    }*/

    public function getOrder($id){
        $data['order']=$this->orderService->getOrder($id);
//        if($data['order']->suit_id){
//            $googService = new GoodService();
//            $data['suit'] = $googService->getSuitGoodById($data['order']->suit_id);
//        }
//        $data['record']=$this->orderService->getRecordByOrderId($id);
        return $data;
    }

    public function getDeliverList(Request $request){
        $data=$request->all();
        foreach ($data as $key => $value) {
            $deliverList[$key]=$this->getOrder($value);
        }
        $settingService=new SettingService;
        $data['deliverList']=$deliverList;
        $data['order']=$settingService->get("ORDER");
        return $data;
    }

    public function deliver(Request $request){
        $data=$request->all();
        try {
            return $this->orderService->deliver($this->checkDeliver($data));
        } catch (\Exception $e) {
            return json_exception_response($e);
        }     
    }

    public function editAddress(Request $request){
        $data=$request->all();
        try {
            return $this->orderService->editAddress($data);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }     
    }

    //验证物流信息
    private function checkDeliver($model)
    {
        $rules=[];
        if($model['type']=="EXPRESS")
        {
            $rules = [
                'type' => ['required'],
                'company'=>['required'],
                'express_code' => ['required', 'numeric'],
            ];
        }
        $validator = app('validator')->make($model, $rules);
        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('物流信息验证失败', $validator->errors());
        }
        return  $model;
    }

}
