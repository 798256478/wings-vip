<?php

namespace App\Events;

use App\Events\CardEvent;
use App\Models\Card;

class SignEvent extends CardEvent
{
    public function __construct(Card $card)
    {
        parent::__construct($card);
    }
    
}