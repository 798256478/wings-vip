<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Http\Request;
use App\Services\AuthService;
use App\Services\LoginRecordService;

class LoginRecordController extends Controller
{
    protected $authService;
    protected $loginRecordService;

    public function __construct(AuthService $authService,LoginRecordService $loginRecordService)
    {
        $this->authService = $authService;
        $this->loginRecordService = $loginRecordService;
    }

    public function getLoginRecords($option,$page,$search=null)
    {
        try {
            $data['loginList'] = $this->loginRecordService->getLoginRecords($option, $search, $page);
            $data['total'] = $this->loginRecordService->getTotal($option, $search);
            return $data;
        }catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

}
