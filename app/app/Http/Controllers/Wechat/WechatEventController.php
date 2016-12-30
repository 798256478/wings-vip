<?php

namespace App\Http\Controllers\wechat;

use App\Http\Controllers\Controller;

use Session;
use Request;
use App;
use App\Models\Card;
use App\Services\CardService;


class WechatEventController extends Controller
{

	public function dispatcher()
	{
        $wechat = App::make('WechatApplication');
        $wechatServer = $wechat->server;

        $wechatServer->setMessageHandler(function($message) use ($wechat){
            // 注意，这里的 $message 不仅仅是用户发来的消息，也可能是事件
            // 当 $message->MsgType 为 event 时为事件
            if ($message->MsgType == 'event') {
                # code...
                switch ($message->Event) {
                    case 'subscribe':
                        # code...
                        $openID = $message->FromUserName;
                        $referrer = substr($message->EventKey,8);
                        $card = Card::where('openid',$openID)->first();
                        if($card && !$card->referrer_id){
                            //事件延迟,用户关注后已经创建过会员卡
                            $card->referrer_id = $referrer;
                            $card->save();
                        }else{
                            //扫描二维码直接建卡并绑定推荐人
                            $wechatUser = $wechat->user;
                            $user = $wechatUser->get($openID)->toArray();
                            $cardService = new CardService();
                            $card = $cardService->createCardByWechatUser($user);
                            if($card){
                                $card->referrer_id = $referrer;
                                $card->save();
                            }
                        }
                        break;
                    default:
                        # code...
                        break;
                }
            }
        });
        $response = $wechatServer->serve();
//        return 111;
        return $response; // Laravel 里请使用：return $response;

    }

    public function setMenu()
    {
        $wechat = App::make('WechatApplication');
        $menu = $wechat->menu;
        $buttons = [
            [
                "type" => "view",
                "name" => "会员中心",
                "url"  => "http://vip.goldwings.cn/wechat"
            ],
            [
                "name"       => "菜单",
                "sub_button" => [
                    [
                        "type" => "click",
                        "name" => "搜索",
                        "key"  => "1"
                    ],
                    [
                        "type" => "click",
                        "name" => "视频",
                        "key"  => "2"
                    ],
                    [
                        "type" => "click",
                        "name" => "赞一下我们",
                        "key" => "3"
                    ],
                ],
            ],
        ];
        $menu->add($buttons);

    }

}