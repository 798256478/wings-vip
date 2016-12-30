<?php

namespace App\Events;

use App\Events\CardEvent;
use App\Models\Card;
use App\Models\Order;

class OrderCompleted extends CardEvent
{
    public $order;
    
    public function __construct(Card $card, Order $order)
    {
        parent::__construct($card);
        $this->order = $order;
    }
    
}