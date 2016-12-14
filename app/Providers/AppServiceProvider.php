<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Config;
use Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('SettingService', function ($app) {
            return new \App\Services\SettingService;
        });
        $this->app->singleton('ProgressService', function ($app) {
            return new \App\Services\Health\ProgressService;
        });
        
        $this->app->singleton('EventRuleService', function ($app) {
            return new \App\Services\EventRuleService;
        });
        
        $this->app->singleton('TemplateMessageService', function ($app) {
            return new \App\Services\TemplateMessageService;
        });
        
        $this->app->singleton('WechatApplication', function ($app) {
            $config = Config::get('customer.' . user_domain() . '.wechat');
            return new \EasyWeChat\Foundation\Application($config);
        });
    }
}
