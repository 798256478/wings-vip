<?php

namespace App\Http\Controllers\Api\Health;

use Dingo\Api\Http\Request;
use App\Services\AuthService;
use App\Services\Health\ExperimentDataService;
use App\Http\Controllers\Api\Controller;

class ExperimentDataController extends Controller
{
    protected $authService;
    protected $ExperimentDataService;
    public function __construct(AuthService $authService, ExperimentDataService $experimentDataService)
    {
        $this->authService = $authService;
        $this->experimentDataService = $experimentDataService;
    }

    public function search($searchDTO)
    {
        try{
             return $this->experimentDataService->search(json_decode($searchDTO));
         }
         catch (\Exception $e){
              return json_exception_response($e);
         }
    }
    
    public function getDetailById($experiment_data_id)
    {
        try{
            $this->authService->singleRoleVerify('admin');
            return $this->experimentDataService->getDetailById($experiment_data_id);
        }
        catch (\Exception $e){
           
        }
    }
    
    public function getRiskById($experiment_data_id)
    {
        try{
            $this->authService->singleRoleVerify('admin');
            return $this->experimentDataService->getRiskById($experiment_data_id);
        }
        catch (\Exception $e){
           
        }
    }

}
