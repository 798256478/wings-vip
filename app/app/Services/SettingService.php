<?php

namespace App\Services;

use App\Models\Setting;
use Config;

class SettingService
{
    private $cache = [];

    public function get($key)
    {
        if (!array_key_exists($key, $this->cache)){
            $setting = Setting::where('key', $key)->first();
            if (isset($setting)){
                $this->cache[$key] = $setting->value;
            }
            else{
                return null;
            }
        }

        return $this->cache[$key];
    }

    public function getAll()
    {
          return Setting::all();
    }

    public function getTheme()
    {
        $enable = config('customer.'.user_domain().'.themes');
        $themes = Config::get('theme');
        $enableThemes = [];
        foreach ($themes as $theme) {
            if (in_array($theme['key'], $enable)) {
                $enableThemes[] = $theme;
            }
        }
        return $enableThemes;
    }

    public function set($key, $value)
    {
        $setting = Setting::where('key', $key)->first();
        if (!isset($setting)){
            $setting = new Setting();
            $setting->key = $key;
        }
        $setting->value = $value;
        $setting->save();

        $this->cache[$key] = $value;
    }

    public function exists($key)
    {
        return $this->get($key) !== null;
    }
}
