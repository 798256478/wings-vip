<?php

namespace App\Events;

use App\Events\CardEvent;
use App\Models\Card;

class ProgressChanged extends CardEvent
{
    public $progress;
    public $name;
    public function __construct(Card $card,$name, $progress)
    {
        parent::__construct($card);
        $this->progress = $progress;
        $this->name = $name;
    }
    
}