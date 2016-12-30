<?php

namespace App\Services\Health;

use DB;
use App\Models\Health\Record;
use App\Models\Health\ProjectData;
use App\Models\Health\RiskData;
use App\Models\Health\ProjectRisk;
use App;

class ProjectDataService
{

    public function saveProjectData($parentProjects, $experiment_data_id)
    {
        $progressService = App::make('ProgressService');
        DB::transaction(function () use ($parentProjects, $experiment_data_id,$progressService) {
            $record = Record::where('experiment_data_id', $experiment_data_id)->first();
            foreach ($parentProjects as $parentProject) {
                if (isset($record->id)) {
                    foreach ($parentProject['childProjects'] as $childProject) {
                        $projectdata = new ProjectData();
                        if (isset($childProject['projectdata_id'])) {
                            $projectdata = ProjectData::where('id', $childProject['projectdata_id'])->first();
                        } else {
                            $projectdata->project_id = $childProject['id'];
                            $projectdata->record_id = $record->id;
                        }
                        $projectrisk = ProjectRisk::where('project_id', $projectdata->project_id)->where('tag', $childProject['abilityLevel'])->first();
                        $projectdata->abilityLevel = $childProject['abilityLevel'];
                        $projectdata->level = $childProject['level'];
                        $projectdata->character = $projectrisk->character;
                        $projectdata->riskAssessment = $projectrisk->instructions;
                        $projectdata->score = $childProject['score'];
                        $projectdata->save();
                    }
                } else {
                    throw new \Dingo\Api\Exception\StoreResourceFailedException('验证失败', ['id' => ['ID错误']]);
                }
            }
            $progressService->changeProgress($experiment_data_id, 8);
        });
    }

    public function saveRiskData($projects, $experiment_data_id)
    {
        $progressService = App::make('ProgressService');
        DB::transaction(function () use ($projects, $experiment_data_id,$progressService) {
            $record = Record::where('experiment_data_id', $experiment_data_id)->first();
            
            foreach ($projects as $project) {
               
                if (isset($record->id)) {
                    $riskData = new RiskData();
                    if (isset($project['riskdata']['id'])) {
                        $riskData = RiskData::find($project['riskdata']['id']);
                    }
                    $riskData->project_id = $project['id'];
                    $riskData->circum_score = $project['riskdata']['circum_score'];
                    $riskData->total_score = $project['riskdata']['total_score'];
                    $riskData->abilityLevel = $project['riskdata']['abilityLevel'];
                    
                    if (isset($project['riskdata']['riskAssessment'])) {
                        $riskData->riskAssessment = $project['riskdata']['riskAssessment'];
                    }
                    $riskData->level = $project['riskdata']['level'];
                    $riskData->project_id = $project['id'];
                    $riskData->record_id = $record->id;
                    $riskData->save();
                } else {
                    throw new \Dingo\Api\Exception\StoreResourceFailedException('验证失败', ['id' => ['ID错误']]);
                }
            }
            $progressService->changeProgress($experiment_data_id, 5);
        });
    }
}
