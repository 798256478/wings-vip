<?php

namespace App\Events;

use App\Events\CardEvent;
use App\Models\Card;
use App\Models\Ticket;

class TicketVerified extends CardEvent
{
    public $ticket;
    public function __construct(Card $card, Ticket $ticket)
    {
        parent::__construct($card);
        $this->ticket = $ticket;
    }
    
}