<?php

namespace App\Events;

use App\Events\CardEvent;
use App\Models\Card;
use App\Models\Property;

class UseProperty extends CardEvent
{
    public $property;
    public function __construct(Card $card, Property $property)
    {
        parent::__construct($card);
        $this->property = $property;
    }
    
}