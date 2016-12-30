<?php

namespace App\Services\Yuda;


class SyncService
{
    public function sendDatatoYuda($data)
    {
        header("Content-Type:text/html;charset=utf-8");
        $param = [
            'type'=>$data['type'],
            'timestamp'=>$data['timestamp'],
            'data'=>$data['data'],
            'sign'=>sha1(strtolower(config('customer.' . user_domain() . '.sync.key').$data['type'].$data['timestamp'].stripslashes(json_encode
                ($data['data'],JSON_UNESCAPED_UNICODE)))),
        ];
        //转换为小写在进行签名
//        echo strtolower(config('customer.' . user_domain() . '.sync.key').$data['type'].$data['timestamp'].stripslashes(json_encode($data['data'],
//                JSON_UNESCAPED_UNICODE))) . '签名结果是' . $param['sign'];
//        echo '<br>';
        $param = stripslashes(json_encode($param,JSON_UNESCAPED_UNICODE));
//        dd($param);
        return $this->curlPost($param,$data['type']);
    }

    public function curlPost($param,$type)
    {
        if($type == 'ORDER'){
            $postUrl = config('customer.' . user_domain() . '.sync.consume');//裕达的订单接口
        }else{
            $postUrl = config('customer.' . user_domain() . '.sync.newMember');//裕达的会员信息接口
        }
//
//        $param = stripslashes($param);
//        $postUrl = config('customer.' . user_domain() . '.sync.receiveTest');//内部模拟测试的接口
//dd($param);
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
//        echo $param;
//        echo '<br>';
//        dd($jsonData);

        if((int)$httpCode <= 0 ){
            $res = [
                'status'=>'FAIL',
                'error_code'=>'连接超时',
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
                    'error_code'=>isset($data['error_code']) ? $data['error_code'] : '返回错误(无错误码)',
                    'back_data'=>$jsonData,
                    'send_data'=>$param,
                    'http_code'=>$httpCode
                ];
                return $res;
            }
        }

    }
}
