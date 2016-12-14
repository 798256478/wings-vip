<?php

namespace App\Http\Controllers\Api\Health;

use Dingo\Api\Http\Request;
use App\Services\AuthService;
use App\Services\Health\SitedataService;
use App\Http\Controllers\Api\Controller;

class SitedataController extends Controller
{
    protected $authService;
    protected $configService;
    protected $experimentService;
    public function __construct(AuthService $authService, SitedataService $sitedataService)
    {
        $this->authService = $authService;
        $this->sitedataService = $sitedataService;
    }

    public function getSiteData(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $form = $request->input('form');
            if (is_array($form)) {
                if (!isset($form['inspector']) || $form['inspector'] == '') {
                    throw new \Dingo\Api\Exception\StoreResourceFailedException('检测员为空', []);
                } elseif (!isset($form['assessor']) || $form['assessor'] == '') {
                    throw new \Dingo\Api\Exception\StoreResourceFailedException('审核员为空', []);
                } elseif (!isset($form['time']) || $form['time'] == '') {
                    throw new \Dingo\Api\Exception\StoreResourceFailedException('检测日期为空', []);
                } elseif (!isset($form['experimentId']) || $form['experimentId'] == '') {
                    throw new \Dingo\Api\Exception\StoreResourceFailedException('实验项目为空', []);
                }
            } else {
                throw new \Dingo\Api\Exception\StoreResourceFailedException('数据错误', []);
            }
            $form['time'] = getTimeFromW3CTime($form['time']);
            $file = $request->file('data');
            $result = $this->sitedataService->getSiteData($form,$file);
            if($result['status'] === 0){
                 return $result['message'];
            }else if($result['status'] === 1 || $result['status'] === 2){
                throw new \Dingo\Api\Exception\StoreResourceFailedException($result['message'], []);
            }else{
                return $result;
            }
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }



    public function editSiteData(Request $request)
    {
        try {
            $siteData = $request->input('data');
            $form = $request->input('form');
            $this->sitedataService->saveSiteData($siteData, $form);
            return '保存成功';
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }
}
