<?php

namespace App\Jobs;

use App\Jobs\CardJob;

use App\Models\PropertyTemplate;
use App\Services\PropertyService;

class GiveProperty extends CardJob
{

    /**
     * Execute the job.
     *
     * @return void
     */
    protected $propertyTemplate;
    protected function getMessageInfo()
    {
         $this->propertyTemplate = PropertyTemplate::find($this->args['propertyTemplateId']);
         $this->message = ['name'=>'property','tag'=>'服务','value'=>$this->propertyTemplate->title,'count'=>$this->args['count']];
    }
    protected function doJob()
    {
        $propertyService = new  PropertyService;
        for ($i=0; $i < count($this->cardIds); $i++) {
            $propertyService->AddProperty($this->cardIds[$i],$this->args['propertyTemplateId'],$this->args['count'],$this->args['validity_days']);
        }
    }
   
}
