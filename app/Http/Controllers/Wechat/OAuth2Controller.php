<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;

use Session;
use Request;
use App;

class OAuth2Controller extends Controller
{
    
    public function callback()
    {
        $wechat = App::make('WechatApplication');
        $oauth = $wechat['oauth'];
        
        // 获取 OAuth 授权结果用户信息
        $user = $oauth->user();
        Session::put('wechat_user', $user->toArray()['original']);
        Session::save();

        $targetUrl = Request::input('target') ?: 'wechat';
        return redirect($targetUrl);
    }
}