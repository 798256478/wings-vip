<?php

namespace App\Listeners;

use App\Events\CardCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\OperatingRecord;

class CardCreatedListener extends CardEventListener
{
    /**
     * Handle the event.
     *
     * @param  CardCreated  $event
     * @return void
     */
    public function handle(CardCreated $event)
    {
        parent::handle($event);
    }
    
    protected function fillOperatingRecord($event, &$record)
    {
        //TODO:添加记录内容
        $record->summary = '领取会员卡';
        $record->action='领取会员卡';
        $record->data=[];
        $record->minimal='领取会员卡';
        $record->summary='您于'.convart_timestamp_to_display(time()).'领取一张会员卡，卡号'.$event->card->card_code;
        $record->event_type = 'CARD_CREATE';
    }
    
    protected function conditionsMatching($event,$conditions=null)
    {
        return true;
    }
    
    protected function getTemplateMessageInfo($event, &$resourceArray)
    {
        return 'CARD_CREATE';
    }
}
