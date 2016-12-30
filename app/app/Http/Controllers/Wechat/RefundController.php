<?php

namespace App\Http\Controllers\Wechat;

use App\Services\RefundService;
use App\Services\OrderService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RefundController extends Controller
{

	public function __construct()
    {
        parent::__construct();
    }
	
    public function newRefund($orderId, $orderDetailId = null)
    {
        $data['order_id'] = $orderId;
        $data['order_detail_id'] = $orderDetailId;
        $orderService=new OrderService;  
        $data['card']=$orderService->getCardByOrderId($orderId);
        $settingService=new SettingService;
        $data['refund_reason']=$settingService->get("ORDER");
        return $this->theme_view('shop.refund',$data);
    }

    public function postRefund(Request $request){
        $data=$request->all();
        $validator = $this->validator($data);
        if ($validator->fails()) {
            $message='';
            foreach($validator->messages()->toArray() as $key){
                $message=$message.implode(',',$key).'  ';
            }
            return response($message, 401);
        }
        $orderService=new OrderService;
        $orderService->updateOrderDetail($data['order_id'], $data['order_detail_id']);
        $RefundService=new RefundService;
        return $RefundService->createRefund($data);
    }


    public function deleteRefund($id){
        $RefundService=new RefundService;
        return $RefundService->cancelRefund($id);
    }

    protected function validator($data)
    {
        $messages = [
                'name.required' => '请输入联系人',
                'phone.required' => '请输入联系电话',
                'phone.regex'=>'手机号格式错误，请确认手机号',
                'reason.required' => '请填写退款原因',
        ];
        $validator = Validator::make($data,
                [
                    'name' => 'required',
                    'phone' => 'required|regex:/^1[345678]{1}\d{9}$/',
                    'reason' => 'required',
                ],
                $messages
            );
        return $validator;
    }

}