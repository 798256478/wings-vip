<?php

namespace App\Listeners;

use App\Events\TicketVerified;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TicketVerifiedListener extends CardEventListener
{

    /**
     * Handle the event.
     *
     * @param  TicketVerified  $event
     * @return void
     */
    public function handle(TicketVerified $event)
    {
        parent::handle($event);
    }
    
    protected function fillOperatingRecord($event, &$record)
    {
        $record->action = '核销卡券';
        $record->data = ['ticket'=>$event->ticket->toArray()];
        $record->minimal = '您使用了一张'.$event->ticket->ticketTemplate->title.'优惠券。';
        $record->summary = '您使用了一张'.$event->ticket->ticketTemplate->title.'优惠券。';
        $record->changes = ['ticket'=>-1];
        $record->event_type = 'TICKET_VERIFIED';
    }
    
    protected function conditionsMatching($event,$conditions=null)
    {
        return true;
    }
    
    protected function getTemplateMessageInfo($event, &$resourceArray)
    {
        $resourceArray['ticket'] = $event->ticket->toArray();
        return 'TICKET_VERIFIED';
    }
}
