<?php

namespace App\Http\Controllers\Api\Health;

use Dingo\Api\Http\Request;
use App\Services\AuthService;
use App\Services\Health\ProjectDataService;
use App\Http\Controllers\Api\Controller;

class ProjectDataController extends Controller
{
    protected $authService;
    protected $projectDataService;
    public function __construct(AuthService $authService, ProjectDataService $projectDataService)
    {
        $this->authService = $authService;
        $this->projectDataService = $projectDataService;
    }


    public function saveProjectData(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $parentProjects = $request->input('data');
            $experiment_data_id = (int) $request->input('experiment_data_id');
            if (is_array($parentProjects)) {
                  foreach ($parentProjects as $parentProject) {
                    foreach ($parentProject['childProjects'] as $childProject) {
                        if (!isset($childProject['score'])) {
                            throw new \Dingo\Api\Exception\StoreResourceFailedException($childProject['name'].'数据为空', []);
                        }
                        if (!isset($childProject['abilityLevel'])) {
                            throw new \Dingo\Api\Exception\StoreResourceFailedException($childProject['name'].'数据为空', []);
                        }
                    }
                }
                $this->projectDataService->saveProjectData($parentProjects, $experiment_data_id);
            } else {
                throw new \Dingo\Api\Exception\StoreResourceFailedException('验证失败', ['data' => ['格式错误']]);
            }
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function saveRiskData(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $projects = $request->input('data');
            $experiment_data_id = (int) $request->input('experiment_data_id');
            $this->projectDataService->saveRiskData($projects, $experiment_data_id);
          
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }
}
