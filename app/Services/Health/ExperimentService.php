<?php
/**
 * Created by PhpStorm.
 * User: shenzhaoke
 * Date: 2016/5/24
 * Time: 11:16
 */

namespace App\Services\Health;

use DB;
use App\Models\Health\Experiment;
use App\Models\Health\ProjectGene;
use App\Models\Health\ProjectSite;
use App\Models\Health\Project;
use App\Models\Health\ProjectRisk;
use App\Models\Health\Site;
use App\Models\Health\CircumRisk;


class ExperimentService
{
    public function get_short_experiments()
    {
          return Experiment::all()->toArray();
    }
    
    public function get_experiments()
    {
       $experiments = Experiment::with(['projects' => function($query)
                        {
                            $query->whereNull('parent');
                        }])->get()->toArray();
       foreach ($experiments as &$model) {
           foreach ($model['projects'] as &$item) {
               $item['subs'] = Project::where('parent',$item['id'])->where('experiment_id',$model['id'])->get();
           }
       }
       return $experiments;
    }
    
    public function get_experiment($id)
    {
       $experiment = Experiment::with(['projects' => function($query)
                        {
                            $query->whereNull('parent');
                        }])->find()->toArray();
        foreach ($model['projects'] as &$item) {
            $item['subs'] = Project::where('parent',$item['id'])->where('experiment_id',$experiment['id'])->get();
        }
       return $experiment;
    }
    
    
    public function get_sites_by_projectId($projectId)
    {
        $data=array();
        $data['project_genes']=ProjectGene::with('gene')->where('project_id',$projectId)->get()->toArray();
        foreach ($data['project_genes'] as &$model) {
            $model['project_sites'] = array();
            $sites=Site::where('gene_id',$model['gene_id'])->get();
            foreach ($sites as $site) {
                $project_sites=ProjectSite::where('project_id',$projectId)->where('code',$site->code)->get()->toArray();
                if(count($project_sites) > 0){
                     array_push($model['project_sites'], $project_sites[0]);
                }
               
            }
        }
        return $data;
    }
    
    public function save_experiment($data)
    {
       $experiment = Experiment::find($data['id']);
       if($experiment == null){
           $experiment=new Experiment;
       }
       $experiment->name=$data['name'];
       $experiment->type=$data['type'];
       $experiment->sampleType=$data['sampleType'];
       $experiment->save();
       return $experiment->id;
    }
    
    public function save_project($data)
    {
       $project=Project::find($data['id']);
       if($project == null){
            $project = new Project;
       }
       $project->experiment_id = $data['experiment_id'];
       $project->name = $data['name'];
       $project->order = $data['order'];
       $project->sitecount = $data['sitecount'];
       $project->method = $data['method'];
       if($data['parent'] != 0)
       $project->parent = $data['parent'];
       $project->save();
       return  $project->id;
    }
    
    public function save_site($data)
    {
          try {
             
            //事务开始
            DB::beginTransaction();
            $projectId=$data[0]['project_id'];
            $project = Project::find($projectId);
            $count = 0;
            foreach ($data as $model) {
                $project_gene;
                $lists=ProjectGene::where('project_id',$projectId)
                                        ->where('gene_id',$model['gene_id'])->get();
                                        
                if(count($lists) > 0){
                    $project_gene = $lists[0];
                    $sql='update project_gene set effect="'.$model['effect'].'" where project_id='.$model['project_id'].' and gene_id='. $model['gene_id'];
                }
                else {
                    $project_gene = new ProjectGene;
                    $sql='insert project_gene(project_id,gene_id,effect,created_at,updated_at) values ('
                        .$model['project_id'].','.$model['gene_id'].',"'.$model['effect'].'","'.time().'","'.time().'")';
                }
                DB::statement(DB::raw($sql));
                $project_sites=$model['project_sites'];
                $count+=count($project_sites);
                foreach ($project_sites as $item) {
                    $sites=ProjectSite::where('project_id',$projectId)
                                        ->where('code',$item['code'])->get();
                                        
                    if(count($sites) > 0){
                        $project_site = $sites[0];
                    }
                    else {
                        $project_site = new ProjectSite;
                    }
                    $project_site->code = $item['code'];
                    $project_site->project_id = $item['project_id'];
                    $project_site->weight = $item['weight'];
                    $project_site->mutation = $item['mutation'];
                    $project_site->isPositive = $item['isPositive'];
                    $project_site->save();
                }
            }
            $project->sitecount = $count;
            $project->save();
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            return json_exception_response($e);
        }
    }

    public function getAllExperiment()
    {
        return Experiment::all();
    }
    
   
    
    public function getRiskByProjectId($projectId)
    {
        $data = array();
        $data['project_risks'] = ProjectRisk::where('project_id',$projectId)->get();
        $data['project_circumrisks'] = CircumRisk::where('project_id',$projectId)->get();
        return $data;
    }
    
    public function save_risk($data)
    {
         ProjectRisk::where('project_id',$data[0]['project_id'])->delete();
         foreach ($data as $item) {
            $projectRisk = new ProjectRisk;
            $projectRisk->tag = $item['tag'];
            $projectRisk->min = $item['min'];
            $projectRisk->max = $item['max'];
            $projectRisk->instructions = $item['instructions'];
            $projectRisk->level = $item['level'];
            $projectRisk->character = $item['character'];
            $projectRisk->project_id = $item['project_id'];
            $projectRisk->save();
         }
    }
    public function save_circumRisk($data)
    {
         CircumRisk::where('project_id',$data[0]['project_id'])->delete();
         foreach ($data as $item) {
            $circumRisk = new CircumRisk;
            $circumRisk->tag = $item['tag'];
            $circumRisk->min = $item['min'];
            $circumRisk->max = $item['max'];
            $circumRisk->level = $item['level'];
            $circumRisk->instructions = $item['instructions'];
            $circumRisk->project_id = $item['project_id'];
            $circumRisk->save();
         }
    }
}
