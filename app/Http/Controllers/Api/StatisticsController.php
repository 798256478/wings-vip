<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Http\Request;
use App\Services\AuthService;
use App\Services\StatisticsService;

class StatisticsController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService, StatisticsService $statisticsService)
    {
        $this->authService = $authService;
        $this->statisticsService = $statisticsService;
    }

    private function checkAuth($key)
    {
        $roles = $this->statisticsService->getSettingVerify($key);
        $roles = $roles ? $roles : 'admin';
        $this->authService->rolesVerifyWithOr($roles);
    }

    public function getDateList()
    {
        try {
            return $this->statisticsService->getDateList();
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function getDaysData()
    {
        try {
            return $this->statisticsService->getDaysData();
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function getStatisticsDate(Request $request)
    {
        try {
            return $this->statisticsService->getStatisticsDate($request);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function getCommoditiesStatistics(Request $request)
    {
        try {
            return $this->statisticsService->getCommoditiesStatistics($request);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function getCommodityStatisticsData(Request $request,$id)
    {
        try {
            return $this->statisticsService->getCommodityStatisticsData($request,$id);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    private function checkPostData($request)
    {
        $rules = [
            'key' => ['required', 'in:CARD,BALANCE,TICKET,PROPERTY,COST'],
            'year' => ['required', 'integer'],
            'month' => ['required', 'string', 'max:3'],
        ];
        $checkArr = ['key', 'year', 'month'];

        $statistics = $request->only($checkArr);
        $validator = app('validator')->make($statistics, $rules);
        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('服务验证失败', $validator->errors());
        }

        return $statistics;
    }
}
