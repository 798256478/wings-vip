<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Http\Request;
// use Dingo\Api\Routing\Helpers;

use App\Services\AuthService;
use App\Services\SettingService;
use App\Services\RefundService;

class RefundController extends Controller
{
    protected $authService;
    protected $refundService;

    public function __construct(AuthService $authService, RefundService $refundService)
    {
        $this->authService = $authService;
        $this->refundService = $refundService;
    }

    public function getRefundsData(Request $request)
    {
        $data=$request->all();
        try {
            return $this->refundService->getRefundsData($data);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function getRefundData($id)
    {
        try {
            $settingService=new SettingService;
            $data['refund_reason']=$settingService->get("ORDER");
            $data['refundData']=$this->refundService->getRefundData($id);
            return $data;
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function dealRefund(Request $request){
        $data=$request->all();
        try {
            if($data['state']=='REFUND'){
                $data=$this->checkRefundInfo($data);
             }
            return $this->refundService->dealRefund($data);
        } catch (\Exception $e) {
            return json_exception_response($e);
        } 
    }

    private function checkRefundInfo($model)
    {
        $rules = [
            'money' => ['required', 'numeric', 'min:0'],
        ];
        $validator = app('validator')->make($model, $rules);
        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('退款信息验证失败', $validator->errors());
        }
        return  $model;
    }
}
