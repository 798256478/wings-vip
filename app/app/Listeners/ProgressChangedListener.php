<?php

namespace App\Listeners;

use App\Events\ProgressChanged;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProgressChangedListener extends CardEventListener
{

    public function handle(ProgressChanged $event)
    {
       
        parent::handle($event);
    }
    
    protected function fillOperatingRecord($event, &$record)
    {
        $record->action = '进度变更';
        $record->data = ['progress'=>$event->progress];
        $record->minimal = '您的'.$event->name.'进度变更为'.$event->progress;
        $record->summary = '您的'.$event->name.'进度变更为'.$event->progress;
        $record->event_type = 'PROGRESS_CHANGED';
    }
    
    protected function conditionsMatching($event,$conditions=null)
    {
        return true;
    }
    
    
    protected function getTemplateMessageInfo($event, &$resourceArray)
    {
        $resourceArray['name'] = $event->name;
        $resourceArray['progress'] = $event->progress;
        return 'PROGRESS_CHANGED';
    }
}
