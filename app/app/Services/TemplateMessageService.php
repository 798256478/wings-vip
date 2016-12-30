<?php

namespace App\Services;

use App\Services\SettingService;
use App\Models\Card;
use App\Models\OperatingRecord;
use App;

class TemplateMessageService
{
    private $settings;

    function __construct()
    {
        $service = app('SettingService');
        $this->settings = $service->get('TEMPLATE_MESSAGES');
    }

    public function send($key, $card, $resource)
    {
        if (array_key_exists($key, $this->settings)){
            $setting = $this->settings[$key];

            if ($card->mobile && array_key_exists('sms', $setting) && $setting['sms']['active']){
                $smsSetting = $setting['sms'];
                $this->sendSmsMessage($smsSetting, $card->mobile, $resource);
            }

            if ($card->openid && array_key_exists('wechat', $setting) && $setting['wechat']['active']){
                $wechatSetting = $setting['wechat'];
                $this->sendWechatMessage($wechatSetting, $card->openid, $resource);
            }
        }
    }

    public function sendSmsMessage($setting, $mobile, $resource)
    {
        // echo 'sendSms';
    }

    public function sendWechatMessage($setting, $openId, $resource)
    {
        $data = [];
        foreach ($setting['data_format'] as $key => $value){
            $data[$key] = $this->replaceByResource($value, $resource);
        }
        try{
            $wechat = App::make('WechatApplication');
            $notice = $wechat['notice'];
            $notice->uses($setting['template_id'])->andData($data)->andReceiver($openId)->send();
        }
        catch (\Exception $e) { 
        }
    }

    public function replaceByResource($str, $resource)
    {
        //匹配方括号
        $tags = [];
        preg_match_all('/\[([\s\S]*?)\]/',$str, $tags);

        foreach ($tags[1] as $tag){
            $val = array_get($resource, $tag);
            if(isset($val) ){
                $str = str_replace('[' . $tag . ']', $val, $str);
            }
        }

        //匹配大括号
        $tags = [];
        preg_match_all('/\{([\s\S]*?)\}/',$str, $tags);
        foreach ($tags[1] as $tag){
            try{
                $result = eval('return ' . $tag . ';');
                $str = str_replace('{' . $tag . '}', $result, $str);
            }
            catch (\Exception $e){

            }
        }

        return $str;
    }

    public function sendSms($mobileStr, $tmpMsg){
        $service = app('SettingService');
        $sms = $service->get('SMS');
        $c = new TopClient;
        $c->appkey = $sms['appkey'];
        $c->secretKey = $sms['secretKey'];
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setExtend("00001");
        $req->setSmsType("normal");
        $req->setSmsFreeSignName($sms['signName']);
        $req->setSmsParam($tmpMsg);
        $req->setRecNum($mobileStr);
        $req->setSmsTemplateCode($sms['smsTmpName']);
        $resp = $c->execute($req);
        return $resp;
    }
}
