<?php

namespace App\Jobs;

use App\Jobs\CardJob;

use App\Models\Ticket;
use App\Models\TicketTemplate;

class GiveTicket extends CardJob
{

    /**
     * Execute the job.
     *
     * @return void
     */
    protected $ticketTemplate;
    protected function getMessageInfo()
    {
         $this->ticketTemplate = TicketTemplate::find($this->args['ticketTemplateId']);
         $this->message = ['name'=>'ticket','tag'=>'优惠券','value'=>$this->ticketTemplate->title,'count'=>$this->args['count']];
    }
    protected function doJob()
    {
        for ($i = 0; $i < count($this->cardIds); $i++) {
            for($j = 0; $j < $this->args['count']; $j++){
                $ticket = new Ticket;
                $ticket->ticket_code = (string)((time() - 1400000000)*10000 + ($i*$this->args['count']+$j));
                $ticket->card_id = $this->cardIds[$i];
                $ticket->ticket_template_id = $this->ticketTemplate->id;
                $ticket->save();
            }
        }
    }
}
