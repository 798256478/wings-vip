<?php

namespace App\Services;

use App;

class ThemeService
{
    public $themeKey = 'default';
    public $colors = [];
    public $texts = [];

    private $views = [];

    public function __construct()
    {
        $settingService = App::make('SettingService');
        $themeSettings = $settingService->get('THEME');

        if (!empty($themeSettings) && !empty($themeSettings['key'])){
            $this->themeKey = $themeSettings['key'];
            $this->colors = $themeSettings['colors'];
            $this->texts = $themeSettings['texts'];
        }
    }

    public function loadWechatCss($fileName)
    {
        $loadString = '<link href="/common/css/wechat/default/' . $fileName . '.css" rel="stylesheet">';
        
        if ($this->themeKey != 'default') {
            $path = public_path() . '\\common\\css\\wechat\\' . $this->themeKey . '\\' . $fileName . '.css';
            if (file_exists($path)){
                $loadString .= '<link href="/common/css/wechat/' . $this->themeKey . '/' . $fileName . '.css" rel="stylesheet">';
            }
        }

        return $loadString;
    }

    public function getViewPath($viewName)
    {
        if (!array_key_exists($viewName, $this->views)) {
             $viewPath = 'wechat.' . $this->themeKey . '.' . $viewName;

            if (view()->exists($viewPath)){
                $this->views[$viewName] = $viewPath;
            } else{
                $this->views[$viewName] = 'wechat.default.'.$viewName;
            }
        }

        return $this->views[$viewName];
    }

    public function getWechatImage($imageName)
    {
        $url = '/common/imgs/wechat/default/' . $imageName;
        if ($this->themeKey != 'default') {
            $path = public_path() . '\\common\\imgs\\wechat\\' . $this->themeKey . '\\' . $imageName;
            if (file_exists($path)){
                $url = '/common/imgs/wechat/' . $this->themeKey . '/' . $imageName;
            }
        }

        return $url;
    }

    public function getColor($key)
    {
        if ($this->colors[$key]){
            return $this->colors[$key];
        }
        else {
            return "";
        }
    }

    public function getText($key)
    {
        if ($this->texts[$key]){
            return $this->texts[$key];
        }
        else {
            return "";
        }
    }
}