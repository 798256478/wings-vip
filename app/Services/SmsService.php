<?php

namespace App\Services;

use TopClient;
use AlibabaAliqinFcSmsNumSendRequest;

class SmsService
{
    public function send($mobile,$type){
        $key = rand(100000,999999);
        $res = $this->sms($mobile, $type, $key);
        if (isset($res->result->success) && $res->result->success) {
            if($type == 'word'){
                session(['word_ver_key'=>$key]);
                session(['word_ver_mobile'=>$mobile]);
                session(['word_ver_key_time'=>time()]);
            }else{
                session(['ver_key'=>$key]);
                session(['ver_mobile'=>$mobile]);
                session(['ver_key_time'=>time()]);
            }
            return true;
        } else {
            return false;
        }
    }

    private function sms($mobile,$type,$key){
        if (!defined("TOP_SDK_WORK_DIR"))
        {
            define("TOP_SDK_WORK_DIR", "/tmp/");
        }

        $smsvalue='{"code":"'.$key.'","product":手机}';
        $config=config('customer.'.user_domain().'.sms');
        $c = new TopClient;
        $c->appkey = $config['appkey'];
        $c->secretKey = $config['secretKey'];
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setExtend("");
        $req->setSmsType("normal");
        $req->setSmsFreeSignName($config['signName']);
        $req->setSmsParam($smsvalue);
        $req->setRecNum($mobile);
        $req->setSmsTemplateCode('SMS_14230022');
        $resp = $c->execute($req);
        return $resp;
    }

}
