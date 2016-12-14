<?php

namespace App\Http\Controllers\Api\Health;

use Dingo\Api\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
// use Dingo\Api\Routing\Helpers;

use App\Services\AuthService;
use App\Services\Health\ExperimentService;
use App\Http\Controllers\Api\Controller;

class ExperimentController extends Controller
{

    protected $authService;
    protected $experimentService;
    public function __construct(AuthService $authService, ExperimentService $experimentService)
    {
        $this->authService = $authService;
        $this->experimentService = $experimentService;
    }
    
    public function get_short_experiments()
    {
         try{
            return $this->experimentService->get_short_experiments();
        }
        catch (\Exception $e){
            return json_exception_response($e);
        }
    }
    public function get_experiments()
    {
       try{
            $this->authService->singleRoleVerify('admin');
            return $this->experimentService->get_experiments();
        }
        catch (\Exception $e){
            return json_exception_response($e);
        }
    }
    
     public function get_experiment($id){
        try{
            $this->authService->singleRoleVerify('admin');
            return $this->experimentService->get_experiment($id);
        }
        catch (\Exception $e){
            return json_exception_response($e);
        }
     }
    
      
    public function get_sites_by_projectId($projectId)
    {
       try{
            $this->authService->singleRoleVerify('admin');
            return $this->experimentService->get_sites_by_projectId($projectId);
        }
        catch (\Exception $e){
            return json_exception_response($e);
        }
    }
    
    public function save_experiment(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $experiment = $this->checkexperiment($request);
            return $this->experimentService->save_experiment($experiment);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }
    
    public function save_project(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $project = $this->checkproject($request);
            return $this->experimentService->save_project($project);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }
    
    public function save_site(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $data=$request->all();
            foreach ($data as $model) {
                $this->checksite($model);
            }
            return $this->experimentService->save_site($data);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }
    
    public function get_risk_by_projectId($projectId)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            return $this->experimentService->getRiskByProjectId($projectId);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }
    
    public function save_risk(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $data=$request->all();
            return $this->experimentService->save_risk($data);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }
    
    public function save_circumRisk(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $data=$request->all();
            return $this->experimentService->save_circumRisk($data);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }
    
    
    
    private  function checkexperiment(Request $request)
    {
        $rules = [
            'name' => ['required'],
            'type' =>['required'],
            'sampleType' => ['required'],
        ];
        $experiment = $request->all();
        $validator = app('validator')->make($experiment, $rules);
        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('实验信息验证失败', $validator->errors());
        }
        return  $experiment;
    }
    
    private  function checkproject(Request $request)
    {
        $rules = [
            'name' => ['required'],
            'order' => ['required'],
            'experiment_id' => ['required'],
        ];
        $project = $request->all();
        $validator = app('validator')->make($project, $rules);
        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('项目信息验证失败', $validator->errors());
        }
        return  $project;
    }
    
    
    private  function checksite($site)
    {
        $rules = [
            'project_id' => ['required'],
            'gene_id' => ['required'],
            // 'mutation' => ['required'],
            // 'effect' => ['required'],
        ];
        $validator = app('validator')->make($site, $rules);
        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('位点验证失败', $validator->errors());
        }
        return  $site;
    }

    public function getAllExperiment()
    {
        return $this->experimentService->getAllExperiment();
    }
    
    

    





}
