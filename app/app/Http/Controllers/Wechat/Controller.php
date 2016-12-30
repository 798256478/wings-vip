<?php

namespace App\Http\Controllers\Wechat;

use App;
use Session;
use Config;
use App\Http\Controllers\Controller as BaseController;
use App\Services\CardService;
use App\Models\Setting;
use App\Services\NoticeService;
use App\Services\ThemeService;

class Controller extends BaseController
{
    protected $cardService;
    protected $currentCard;
    protected $settingService;
    protected $newCard = false;
    
    
    public function __construct()
    {
        //parent::__construct();

        if (!Session::has('wechat_user')){
            abort(404);
        }
        $wechatUser = Session::get('wechat_user');
        $this->enableModules = Config::get('customer.' . user_domain() . '.enable_modules');
        $this->cardService = new CardService();
        $this->settingService = App::make('SettingService');
        $this->currentCard = $this->cardService->getCardByOpenId($wechatUser['openid']);
        
        if (empty($this->currentCard)){
            $this->currentCard = $this->cardService->createCardByWechatUser($wechatUser);
            if(!$this->currentCard){
                die(view('wechat.default.sync_error'));
            }else{
                $this->newCard = true;
            }
        }
        else{
            $this->tryRefreshCurrentCard($wechatUser);
        }
    }
    
    private function tryRefreshCurrentCard($wechatUser)
    {
        if (
            $this->currentCard->nickname !== $wechatUser['nickname'] ||
            $this->currentCard->headimgurl !== $wechatUser['headimgurl']
        ){
            $this->currentCard->nickname = $wechatUser['nickname'];
            $this->currentCard->headimgurl = $wechatUser['headimgurl'];
            $this->currentCard->save();
        }
    }
    
    protected function theme_view($viewName, $data = [], $JssdkEnabled = false)
    {
        //加载激活模块列表
        $data['enable_modules'] = $this->enableModules;

        //加载会员卡信息
        $data['card_info'] = $this->currentCard;

        //加载微信JSAPI配置 
        if ($JssdkEnabled) {
            $wechat = App::make('WechatApplication');
            $data['wx_js'] = $wechat->js;
        }

        //加载通知
        $noticeSerivce = new NoticeService;
        $notices = $noticeSerivce->getNoticesByCardId($this->currentCard->id);
        if ($notices){
            $data['notices'] = $notices;
        }

        //加载主题
        $themeService = new ThemeService;
        $data['theme'] = $themeService;

        //返回主题视图
        return view($themeService->getViewPath($viewName), $data);
    }
}
