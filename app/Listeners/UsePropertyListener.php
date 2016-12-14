<?php

namespace App\Listeners;

use App\Events\UseProperty;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UsePropertyListener extends CardEventListener
{

    /**
     * Handle the event.
     *
     * @param  UseProperty  $event
     * @return void
     */
    public function handle(UseProperty $event)
    {
        parent::handle($event);
    }
    
    protected function fillOperatingRecord($event, &$record)
    {
        $record->action = '服务体验';
        $record->data = ['property'=>$event->property->toArray()];
        $record->minimal = $event->property->propertyTemplate->title;
        $record->summary = '您体验了'.$event->property->propertyTemplate->title.'服务一次。';
        $record->changes = ['property'=>-1];
        $record->event_type = 'USE_PROPERTY';
    }
    
    protected function conditionsMatching($event,$conditions=null)
    {
        return true;
    }
    
    protected function getTemplateMessageInfo($event, &$resourceArray)
    {
        $resourceArray['property'] = $event->property->toArray();
        return 'USE_PROPERTY';
    }
}
