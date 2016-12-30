<?php

namespace App\Services\Health;

use DB;
use App\Models\Health\ProjectGene;
use App\Models\Health\ProjectSite;
use App\Models\Health\Project;
use App\Models\Health\Gene;
use App\Models\Health\Site;
use App\Models\Health\Experiment;
use App\Models\Health\ProjectRisk;
use App\Models\Health\CircumRisk;

class ConfigService
{
    public function saveSetting($projectsList, $experiment)
    {
  
        DB::transaction(function () use ($projectsList, $experiment) {
            $exp = new Experiment();
            $exp->name = $experiment['name'];
            $exp->type = $experiment['type'];
            $exp->sampleType = '口腔黏膜';
            $exp->save();
            // $tags = [
            //             ['name'=> '一般风险'] ,
            //             ['name'=> '中度风险'] ,
            //             ['name'=> '高度风险'] 
            //         ];
            foreach ($projectsList as $key => $value) {
                $parentProject = new Project();
                $parentProject->name = $key;
                $parentProject->experiment_id = $exp->id;
                $parentProject->method = '';
                $parentProject->save();
                // foreach ($tags as $tag) {
                //     $projectRisk = new ProjectRisk();
                //     $projectRisk->project_id = $parentProject->id;
                //     $projectRisk->tag = $tag['name'];
                //     $projectRisk->save();
                // }
                foreach ($value as $projectvalue) {
                    $project = new Project();
                    $project->name = $projectvalue['name'];
                    $project->experiment_id = $exp->id;
                    $project->method = $projectvalue['method'];
                    $project->parent = $parentProject->id;
                    $project->save();
                    $i=1;
                    foreach ($projectvalue['projectRisk'] as $risk) {
                        $projectRisk = new ProjectRisk();
                        $projectRisk->project_id = $project->id;
                        $projectRisk->tag = $risk->name;
                        if(isset($risk->min)){
                              $projectRisk->min = $risk->min;
                        }
                        if(isset($risk->max)){
                            $projectRisk->max = $risk->max;
                        }
                        if(isset($risk->character)){
                            $projectRisk->character = $risk->character;
                        }
                        if(isset($risk->reminder)){
                            $projectRisk->instructions = $risk->reminder;
                        }
                        $projectRisk->level = $i;
                        $projectRisk->save();
                        $i++;
                    }
                  
                    $this->circumRisk($project->id);
 
                    foreach ($projectvalue['genes'] as $gene) {
                        $myGene = $this->geneExist($gene['name']);
                        if ($myGene === false) {
                            $myGene = new Gene();
                            $myGene->name = $gene['name'];
                            $myGene->default_is_positive = $gene['default_is_positive'];
                            $myGene->site_count = count($gene['sites']);
                            $myGene->default_effect = $gene['default_effect'];
                            $myGene->save();
                        }
       
                        $projectGene = new ProjectGene();
                        $projectGene->project_id = $project->id;
                        $projectGene->gene_id = $myGene->id;
                        $projectGene->effect = $gene['default_effect'];
                        $projectGene->save();
                        $project->sitecount += count($gene['sites']);
                        $project->save();
                        foreach ($gene['sites'] as $site) {
                            $mysite = $this->siteExist($site['code']);
                            if ($mysite === false) {
                                $mysite = new Site();
                                $mysite->code = $site['code'];
                                $mysite->rs_code = $site['rs_code'];
                                $mysite->mutation = $site['mutation'];
                                $mysite->SNPSite = $site['SNPSite'];
                                $mysite->DNASingleType = 1;
                                $mysite->gene_id = $myGene->id;
                                $mysite->save();
                            }
                            $projectSite = new ProjectSite();
                            $projectSite->code = $mysite->code;
                            $projectSite->project_id = $project->id;
                            $projectSite->weight = $site['weight'];
                            $projectSite->isPositive = 1;
                            $projectSite->mutation =  $site['mutation'];;
                            $projectSite->save();
                        }
                    }
                }
            }
        });
    }
    
   private function circumRisk($projectId)
   {
        $circumRisk1 = new CircumRisk();
        $circumRisk1->project_id = $projectId;
        $circumRisk1->tag = '良好';
        $circumRisk1->min = -100;
        $circumRisk1->max = 35;
        $circumRisk1->instructions = '';
        $circumRisk1->level = 3;
        $circumRisk1->save();
        $circumRisk2 = new CircumRisk();
        $circumRisk2->project_id = $projectId;
        $circumRisk2->tag = '较差';
        $circumRisk2->min = 35;
        $circumRisk2->max = 55;
        $circumRisk2->instructions = '';
        $circumRisk2->level = 2;
        $circumRisk2->save();
        
        $circumRisk3 = new CircumRisk();
        $circumRisk3->project_id = $projectId;
        $circumRisk3->tag = '差';
        $circumRisk3->min = 55;
        $circumRisk3->max = 100;
        $circumRisk3->instructions = '';
        $circumRisk3->level = 1;
        $circumRisk3->save();
                    
   }

    public function geneExist($geneName)
    {
        $gene = Gene::where('name', $geneName)->with('sites')->get();

        return count($gene) > 0 ? $gene[0] : false;
    }

    public function siteExist($siteName)
    {
        $site = Site::where('code', $siteName)->get();

        return count($site) > 0 ? $site[0] : false;
    }
}
