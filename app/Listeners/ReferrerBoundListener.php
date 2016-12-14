<?php

namespace App\Listeners;

use App\Events\ReferrerBound;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\OperatingRecord;

class ReferrerBoundListener extends CardEventListener
{
    /**
     * Handle the event.
     *
     * @param  ReferrerBound  $event
     * @return void
     */
    public function handle(ReferrerBound $event)
    {
        parent::handle($event);
    }
    
    protected function fillOperatingRecord($event, &$record)
    {
        //TODO:添加记录内容
        $record->summary = '您于'.time().'绑定推荐人';
        $record->action='绑定推荐人';
        $record->data=[];
        $record->minimal='绑定推荐人';
        $record->event_type = 'REFERRER_BOUND';
    }
    
    protected function conditionsMatching($event,$conditions=null)
    {
        return true;
    }
    
    
    protected function getTemplateMessageInfo($event, &$resourceArray)
    {
        return 'REFERRER_BOUND';
    }
}
