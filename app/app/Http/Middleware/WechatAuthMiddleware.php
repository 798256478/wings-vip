<?php

namespace App\Http\Middleware;

use Closure;
use App;
use Session;
use App\Services\CardService;

class WechatAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 未登录
        if (!Session::has('wechat_user')) {
            
            $target = $request->path();
            $redirectUrl = action('wechat\OAuth2Controller@callback', ['target' => $target]);
            
            $wechat = App::make('WechatApplication');
            $oauth = $wechat['oauth'];
            return $oauth->scopes(['snsapi_userinfo'])->redirect($redirectUrl);
            //$oauth->redirect()->send();
        } 
        
         return $next($request);
    }
}
