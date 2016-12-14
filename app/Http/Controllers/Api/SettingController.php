<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
// use Dingo\Api\Routing\Helpers;

use App\Services\AuthService;
use App\Services\SettingService;

class SettingController extends Controller
{

    protected $authService;
    protected $settingService;
    public function __construct(AuthService $authService, SettingService $settingService)
    {
        $this->authService = $authService;
        $this->settingService = $settingService;
    }

    public function getSetting($key='')
    {
        if(strlen($key)==0){
           throw new \Dingo\Api\Exception\StoreResourceFailedException("关键字错误", $validator->errors());
        }
        return $this->settingService->get($key);
    }
    public function getAll()
    {
        return $this->settingService->getAll();

    }
    public function setSetting(Request $request)
    {
       $key=$request->input('key');
       $value=$request->input('value');
       if(strlen($key)==0){
            throw new \Dingo\Api\Exception\StoreResourceFailedException("关键字错误", $validator->errors());
       }
       $this->settingService->set($key,$value);
    }

    public function getTheme()
    {
        try {
            return $this->settingService->getTheme();
        } catch (\Exception $e) {
            return json_exception_response($e);
        }

    }

    public function getEnableModules()
    {
        try {
            return config('customer.'.user_domain().'.enable_modules');
        } catch (\Exception $e) {
            return json_exception_response($e);
        }

    }
}
