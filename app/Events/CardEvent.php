<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

use App\Models\Card;

abstract class CardEvent extends Event
{
    use SerializesModels;
    
    public $card;

    public function __construct(Card $card)
    {
        $this->card = $card;
    }
}
