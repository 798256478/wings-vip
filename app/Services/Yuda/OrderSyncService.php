<?php

namespace App\Services\Yuda;
use App\Models\Yuda\OrderSyncFail;
use App\Services\Yuda\SyncService;

class OrderSyncService
{
//    得到同步失败记录
    public function getSyncFailRecord($param)
    {
        $length = 15;
        $data = [];
        if(isset($param['page']) && $param['page'] > 0){
            $query = OrderSyncFail::with('order','card');
            if(!isset($param['isRepair']) || !$param['isRepair']){//只显示未修复的记录
                $query = $query->where('state','FAIL');
            }
            $data['total'] = $query->count();
            $data['syncFailList'] = $query->orderBy('created_at', 'desc')->skip($length * ($param['page'] - 1))->take($length)->get();
            return $data;
        }else{
            throw new \Dingo\Api\Exception\StoreResourceFailedException('页码错误', ['page' => ['页码必须大于0']]);
        }
    }

//    同步成功
    public function syncSuccess($id)
    {
        OrderSyncFail::where('id',$id)->update(['state'=>'SUCCEED']);
    }

//    重新发送同步请求
    public function againSync($id)
    {
        $syncData = OrderSyncFail::where('id',$id)->first();
        $syncService = new SyncService();
        $res = $syncService->curlPost($syncData->send_data);
        if($res['status'] === 'SUCCEED'){
            $this->syncSuccess($id);
            return true;
        }else{
            return false;
        }

    }

    public function sendDatatoYuda($data)
    {
        $param = [
            'type'=>$data['type'],
            'timestamp'=>$data['timestamp'],
            'data'=>$data['data'],
            'sign'=>sha1(strtolower(config('yuda.key').$data['type'].$data['timestamp'].json_encode($data['data'],
                    JSON_UNESCAPED_UNICODE))),
        ];
        $postUrl = config('yuda.receiveTest');//内部模拟测试的接口
//        $postUrl = config('yuda.url');//裕达的接口
        $param = json_encode($param,JSON_UNESCAPED_UNICODE);
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_TIMEOUT,3);//超时设置
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($param))
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $jsonData = curl_exec($ch);//运行curl
        $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);

        if(substr($httpCode,0,1) !=2 ){
            $res = [
                'status'=>'FAIL',
                'error_code'=>'远程异常',
                'back_data'=>$data,
                'send_data'=>$param,
                'http_code'=>$httpCode
            ];
            return $res;
        }else{
            $data = json_decode($jsonData,true);
            if(isset($data['status']) && $data['status'] === 'SUCCEED'){
                return $data;
            }else{
                $res = [
                    'status'=>'FAIL',
                    'error_code'=>isset($data['error_code']) ? $data['error_code'] : '返回错误',
                    'back_data'=>$jsonData,
                    'send_data'=>$param,
                    'http_code'=>$httpCode
                ];
                return $res;
            }
        }
    }
}
