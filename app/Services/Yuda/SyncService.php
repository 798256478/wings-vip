<?php

namespace App\Services\Yuda;


class SyncService
{
    public function sendDatatoYuda($data)
    {
        $param = [
            'type'=>$data['type'],
            'timestamp'=>$data['timestamp'],
            'data'=>$data['data'],
            'sign'=>sha1(strtolower(config('yuda.key').$data['type'].$data['timestamp'].json_encode($data['data'],
                    JSON_UNESCAPED_UNICODE))),
        ];
        $param = json_encode($param,JSON_UNESCAPED_UNICODE);
        return $this->curlPost($param);
    }

    public function curlPost($param)
    {
//        $postUrl = config('yuda.receiveTest');//内部模拟测试的接口
        $postUrl = config('yuda.url');//裕达的接口
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
                'back_data'=>$jsonData,
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
