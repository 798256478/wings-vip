<?php

namespace App\Listeners;

use App\Events\ProfileFilled;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProfileFilledListener extends CardEventListener
{

    /**
     * Handle the event.
     *
     * @param  ProfileFilled  $event
     * @return void
     */
    public function handle(ProfileFilled $event)
    {
        parent::handle($event);
    }
    
    protected function fillOperatingRecord($event, &$record)
    {
         $record->summary = '您于'.time().'完善资料';
         $record->action='完善资料';
         $record->data=[];
         $record->minimal='完善资料';
         $record->event_type = 'PROFILE_FILLED';
    }
    
    protected function conditionsMatching($event,$conditions=null)
    {
        return true;
    }
    
    protected function getTemplateMessageInfo($event, &$resourceArray)
    {
        return 'PROFILE_FILLED';
    }
}
