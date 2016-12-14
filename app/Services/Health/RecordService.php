<?php
/**
 * Created by PhpStorm.
 * User: shenzhaoke
 * Date: 2016/5/24
 * Time: 11:16
 */

namespace App\Services\Health;

use DB;
use App\Models\Health\Record;
use App\Models\Health\Project;
use App\Models\Health\Customer;
use App\Models\Health\Experiment;
use App\Models\Health\ProjectData;
use App\Models\Health\ProjectGene;
use App\Models\Health\ProjectSite;
use App\Models\Health\ExperimentData;
use App\Models\Health\SiteData;

class RecordService
{
    public function get_records($barcode,$experimentid,$pageindex,$pagesize)
    {
        if($barcode == '*')
            $barcode='';
           
        $where=Record::with(['experimentData' => function($query) use ($barcode,$experimentid){
             $query->with(['barcode' => function($childquery) use ($barcode){
                 $childquery->where('code', 'like', '%'.$barcode.'%');
             },'experiment'])->whereIn('progress_id',[5,6,8]);
             if($experimentid!='*')
                $query->where('experiment_id',$experimentid);
        }]);
        $data['count'] = $where->count();
        $data['records'] = $where->skip($pagesize*($pageindex - 1))
                            ->take($pagesize)->orderBy('created_at', 'desc')->get()->toArray();
        foreach ( $data['records'] as &$model) {
           $model['barcode_str'] =  $model['experiment_data']['barcode']['code'];
           $this->fullRecord($model);
           unset($model['experiment_data']);
        }    
       
        return $data;
    }
    
    public function get_by_id($id)
    {
        $record = Record::with('experimentData.barcode' ,'experimentData.experiment.projects')->find($id)->toArray();
        $this->fullRecord($record);
        foreach ($record['experiment_data']['experiment']['projects'] as &$model) {
            if($model['parent'] != null){
                $model['parentName'] = Project::find($model['parent'])->name;
                $project_data = ProjectData::where('project_id',$model['id'])->where('record_id',$id)->first();
                if($project_data != null){
                      $model['project_dto'] = $project_data->toArray();
                }
                $model['gene_dtos'] = $this->get_detail_by_projectId($id,$model['id']);
            }
        }
        $data['record']=$record;
        return $data;
    }
    
    public function get_by_projectid($experiment_data_id,$project_id)
    {
         $project = Project::find($project_id);
         $data['parentName'] = $project->name;
         $data['project_id'] = $project_id;
         $data['experiment_data_id'] = $experiment_data_id;
         $experiment_data = ExperimentData::find($experiment_data_id);
         $record = Record::where('experiment_data_id',$experiment_data_id)->first();
         $projectDetails = Project::where('parent',$project_id)->where('experiment_id', $experiment_data->experiment_id)->get()->toArray();
         foreach ($projectDetails as &$model) {
            if($model['parent'] != null){
                // $model['parentName'] = Project::find($model['parent'])->name;
                $project_data = ProjectData::where('project_id',$model['id'])->where('record_id',$record->id)->first();
                if($project_data != null){
                      $model['project_dto'] = $project_data->toArray();
                }
                $model['gene_dtos'] = $this->get_detail_by_projectId($record->id,$model['id']);
            }
        }
        $data['projectDetails'] =  $projectDetails;
        return $data;
    }
    

    
    private function fullRecord(&$record)
    {
        $customers = Customer::where('barcode_id', $record['experiment_data']['barcode_id'])->get();
        if(count($customers) > 0){
            $record['customerName'] = $customers[0]->name;
            $record['customerAge'] = $customers[0]->age;
            if($customers[0]->sex === 1)
                $record['customerSex'] = '男';
            else if($customers[0]->sex === 0)
                $record['customerSex'] = '女';
            else {
                 $record['customerSex'] = '未知';
            }
        }
        else {
            $model['customerName'] = '';
            $model['customerAge'] = '';
            $model['customerSex'] = '';
        }
        $experiment= $record['experiment_data']['experiment'];
        if($experiment != null){
            $record['experimentName'] = $experiment['name'];
            $record['experimentType'] = $experiment['type'];
        }
        else{
            $record['experimentName'] = '';
            $record['experimentType'] = '';
        }
        $record['sampleType'] = $experiment['sampleType'];
    }
    
    public function getParent($experiment_data_id)
    {
        $experiment_data=ExperimentData::find($experiment_data_id);
        $customer = Customer::where('barcode_id', $experiment_data->barcode_id)->first();
        $data['projects'] = Project::where('parent',null)->where('experiment_id',$experiment_data->experiment_id)->get();
        $data['customerName'] = $customer->name;
        $data['code'] = $experiment_data->barcode->code;
        $data['experimentName'] = $experiment_data->experiment->name;
        $data['created_at'] = $experiment_data->experiment->created_at;
        $data['experiment_data'] = $experiment_data;
        return  $data;
    }
    
    private function get_detail_by_projectId($recordId,$projectId)
    {
        $gene_dtos=array();
        $project_genes=ProjectGene::with('gene')->where('project_id',$projectId)->get();
        $experimentDataService=new ExperimentDataService;
        foreach ($project_genes as $project_gene) {
            $gene_dto=array();
            $gene_dto['effect']=$project_gene->effect;
            $gene_dto['name']=$project_gene->gene->name;
            $gene_dto['site_dtos'] = array();
            $project_sites = ProjectSite::with(['site'=> function($query) use ($project_gene)
                            {
                                $query->where('gene_id',$project_gene->gene->id);
                            }])->where('project_id',$projectId)->get();
            foreach ($project_sites as $project_site) {
                if($project_site->site != null){
                    $site_dto = $experimentDataService->convert_projectsite_to_DTO($project_site,$recordId);
                    array_push($gene_dto['site_dtos'] , $site_dto);
                }
            }
            array_push($gene_dtos,$gene_dto);
        }
        return $gene_dtos;
    }
}


