<?php

namespace App\Jobs;

use App\Jobs\CardJob;
use App\Models\Card;
use App\Services\SettingService;

class LevelUp extends CardJob
{
    protected $card;//提升等级不可能群发
    protected $isLevelUp;
    protected $newLevel;
    protected function getMessageInfo()
    {
        $this->card=Card::find($this->cardIds[0]);
        $this->newLevel=null;
        $this->isLevelUp=$this->judgeLevelUp($this->card->level, $this->args,$this->newLevel);
        if($this->isLevelUp){
             $this->message = ['name'=>'level','tag'=>'提升等级','value'=>$this->newLevel['name']];
        }
        else{
             $this->message = null;
        }
       
    }

    protected function doJob()
    {
        if($this->isLevelUp){
            $card=Card::find( $this->card->id);
            $card->level=$this->newLevel['id'];
            $card->save(); 
        }
    }
    //判断是否提升等级
    protected function judgeLevelUp($oldLevelId,$newLevelId,&$newLevel)
    {
       $settingService = new  SettingService;
       $levels=$settingService->get('CARD')['levels'];
       $oldLevel = null;
       foreach ($levels as $model) {
          if($model['id'] == $oldLevelId){
              $oldLevel = $model;
          }
          if($model['id'] == $newLevelId){
               $newLevel=$model;
          }
       }
       if($oldLevel != null && $newLevel != null){
           if($newLevel['order']>$oldLevel['order'])
            return true;
       }
      return false;
    }
}
