<?php 

namespace App\Services\Health;
use App\Models\Health\Customer;
use App\Models\Health\Detection;
use App\Models\Health\Experiment;
use App\Exceptions\WingException;
use App\Models\Health\Barcode;
use App\Models\Health\ExperimentData;
use App\Models\Health\Progress;
use App\Models\Health\ProjectSite;
use App\Models\Health\ProjectData;
use App\Models\Health\RiskData;
use App\Models\Health\Project;
use App\Models\Health\Record;
use App\Models\Health\SiteData;
use App\Services\Health\ProjectDataService;

class ExperimentDataService
{
    //根据会员卡id获取名下的所有检测
    public function getListByCardId($id)
    {
        $customers=Customer::where('card_id',$id)->orderBy('created_at','desc')->get();
        $result = array();
        foreach ($customers as $customer) {
            if($customer['barcode_id']!=null)
            {
               $experiment_datas=ExperimentData::with('experiment','progress')->where('barcode_id',$customer->barcode_id)->get();
               foreach ($experiment_datas as $experiment_data) {
                   $model=array();
                   $model['id'] = $experiment_data->id;
                   $model['experiment_name'] = $experiment_data->experiment->name;
                   $model['customer_name'] = $customer->name;
                   $model['created_at'] = $customer->created_at;
                   if( $experiment_data->progress == null){
                       $model['nowProgress'] = '暂无进度';
                   }else {
                       $model['nowProgress'] = $experiment_data->progress->name;
                   }
                   array_push($result,$model);
               } 
            }
        }
        return $result;
    }
    
    //根据条件查询检测列表
    public function search($searchDTO)
    {
         $search = ExperimentData::with(['barcode' => function($query) use ($searchDTO)
         {
                 $query->where('code', 'like', $searchDTO->code.'%');
        },'progress','experiment' ]);
        if($searchDTO->experimentId!=0){
            $search = $search->where('experiment_id',$searchDTO->experimentId);
        }
        if (is_array($searchDTO->progress)) {
            $search = $search->whereIn('progress_id', $searchDTO->progress);
        }
        if(is_int($searchDTO->progress)){
             $search = $search->where('progress_id', $searchDTO->progress);
        }
        $data['data'] = $search->orderBy('id', 'desc')->skip($searchDTO->pageSize * ($searchDTO->page - 1))->take($searchDTO->pageSize)->get();
        $data['total']= $search->count();
        return $data;
    }

    //根据检测id查询基础信息
    public function getbyId($experiment_data_id)
    {
        $experiment_data = ExperimentData::find($experiment_data_id);
        $customer=Customer::where('barcode_id',$experiment_data->barcode_id)->first();
        $data['id']= $experiment_data->id;
        $data['experiment_name']= $experiment_data->experiment->name;
        $data['barcode_id']= $experiment_data->barcode_id;
        $data['created_at']= $customer->created_at;
        $data['customer_name']= $customer->name;
        $data['code']= $experiment_data->barcode->code;
        return $data;
    }
    //根据检测id查询详细信息（项目，位点以及结果）
    public function getDetailById($experiment_data_id)
    {
        $experimentData = ExperimentData::find($experiment_data_id);
        
        if (isset($experimentData->id)) {
            $record = Record::where('experiment_data_id', $experimentData->id)->first();
            $projectdatas = ProjectData::where('record_id', $record->id)->get()->toArray();
            $parentProjects = Project::select('id', 'name')->where('experiment_id', $experimentData->experiment_id)
                ->whereNull('parent')->with('projectRisks')->get()->toArray();
            foreach ($parentProjects as &$parentProject) {
                $parentProject['childProjects'] = Project::select('id', 'name')->where('parent', $parentProject['id'])->with('projectRisks')->get()->toArray();
                foreach ($parentProject['childProjects'] as &$childProject) {
                    $this->get_detailinfo_by_Project($record->id,$childProject,$projectdatas);
                }
            }
            return $parentProjects;
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('出现错误', []);
        }
    }
    
    //根据检测id查询有风险的项目详细信息
    public function getRiskById($experiment_data_id)
    {
        $experimentData = ExperimentData::find($experiment_data_id);
        if (isset($experimentData->id)) {
            $record = Record::where('experiment_data_id', $experimentData->id)->first();
            $projects = Project::select('id', 'name')->where('experiment_id', $experimentData->experiment_id)
               ->whereNotNull('parent')->with('CircumRisks')->get()->toArray();
            $projectdatas = ProjectData::where('record_id', $record->id)->where('level','>',1)->get()->toArray();
            $riskdatas = RiskData::where('record_id',$record->id)->get()->toArray();
            $projectList = [];
            foreach ($projects as $project) {
                $model=array();
                $model['id'] = $project['id'];
                $model['name'] = $project['name'];
                $model['circumRisks'] = $project['circum_risks'];
                foreach ($projectdatas as $projectdata) {
                    if ($projectdata['project_id'] == $project['id']) {
                        $model['projectdata'] = $projectdata;
                        foreach ($riskdatas as $riskdata) {
                            if ($riskdata['project_id'] == $project['id']) {
                                $model['riskdata'] = $riskdata;
                            }
                        }
                        array_push($projectList, $model);
                    }
               }
            }
            return $projectList;
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('出现错误', []);
        }
    }
    
    //获取项目的详细信息
    private function get_detailinfo_by_Project($recordid,&$project,$projectdatas)
    {
        $score=0;
        $project_sites = ProjectSite::with('site')->where('project_id',$project['id'])->get();
        $project['siteDTOs']=array();
        foreach ($project_sites as $project_site) {
            $siteDTO=$this->convert_projectsite_to_DTO($project_site,$recordid);
            array_push($project['siteDTOs'], $siteDTO);
            $score=(float)$score + $siteDTO['score'];
            $project['original_score']= (float)(number_format($score, 2, '.', ''));
            if($projectdatas==null){
                $project['abilityLevel'] = null;
                $project['level'] = null;
                $project['score'] = $project['original_score'];
            }
            else{
                 foreach ($projectdatas as $projectdata) {
                    if ($projectdata['project_id'] == $project['id']) {
                        $project['score'] = (float)(number_format($projectdata['score'], 2, '.', ''));
                        $project['abilityLevel'] = $projectdata['abilityLevel'];
                        $project['level'] = $projectdata['level'];
                        $project['projectdata_id'] = $projectdata['id'];
                    }
                }
            }
        }
    }
    
    
    public function convert_projectsite_to_DTO($project_site,$recordid)
    {
        $site_data = SiteData::where('record_id',$recordid)->where('code', $project_site->code)->first();
        $siteDTO=array();
        $siteDTO['SNPSite'] =$project_site->site->SNPSite;
        $siteDTO['mutation'] =$project_site->mutation;
        $siteDTO['showGenotype'] = $site_data->showGenotype;
        $siteDTO['code'] = $project_site->site->code;
        $this->calculateSiteScore($project_site->weight,$site_data->showGenotype,$siteDTO);
        if($siteDTO['score']<0){
            $siteDTO['color'] = '24,154,21' ;
        }
        else if($siteDTO['score'] == 0){
            $siteDTO['color'] ='' ;
        }
        else{
             if($siteDTO['showGenotype']==$siteDTO['mutation'].$siteDTO['mutation']){
                   $siteDTO['color'] ='255,0,0';
             }
             else{
                  $siteDTO['color'] ='205,212,7';
             }
        }
        return  $siteDTO;
    }
    
    private function calculateSiteScore($weight,$showGenotype,&$siteDTO)
    {
        $match='';
        if(isset($weight[$showGenotype])){
            $match=$showGenotype;
        }
        else{  //'GA'和'AG'等同
            $length=strlen($showGenotype)/2;
            $first=substr($showGenotype,$length);
            $end=substr($showGenotype,-$length);
            $match=$end . $first;
        }
        if(strlen($match)>0){
            if($weight[$match]['score'] != null){
                 $siteDTO['score'] = (float)(number_format((float)$weight[$match]['score'], 2, '.', ''));
            }
            else{
                $siteDTO['score'] = 0;
                $siteDTO['abilityLevel'] = $weight[$match]['tag'];
                $siteDTO['mean'] = $weight[$match]['mean'];
            }
        }
        else {
            $siteDTO['score'] = 0;
        }
    }

}