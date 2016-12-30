<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
// use Dingo\Api\Routing\Helpers;

use App\Services\AuthService;
use App\Services\OperatingRecordService;

class OperatingRecordController extends Controller
{

    protected $authService;
    protected $operatingRecordService;

    public function __construct(AuthService $authService,OperatingRecordService $operatingRecordService)
    {
        $this->operatingRecordService = $operatingRecordService;
        $this->authService = $authService;
    }
    
    public function  getstatistical()
    {
        try{
            $user = $this->authService->getAuthenticatedUser();
            return $this->operatingRecordService->statistical($user);
        }
        catch (\Exception $e) {
              return json_exception_response($e);
         } 
    }

    public function getOperatingRecords($page,$search)
    {
        $search=json_decode($search,true);
//        return gettype($search);
        try{
            $data['operatingList']=$this->operatingRecordService->getOperatingList($page,$search);
            $data['total']=$this->operatingRecordService->getTotal($search);
            return $data;
        }catch (\Exception $e) {
            return json_exception_response($e);
        }

    }
    
}
