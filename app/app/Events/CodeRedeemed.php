<?php

namespace App\Events;

use App\Events\CardEvent;
use App\Models\Card;

class CodeRedeemed extends CardEvent
{
    public $code;
    public $jobResult;
    public function __construct(Card $card,$jobResult, $code)
    {
        parent::__construct($card);
        $this->jobResult = $jobResult;
        $this->code = $code;
    }
    
}
