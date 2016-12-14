<?php

namespace App\Jobs;
use App\Models\Card;


class PayCommission extends CardJob
{

    protected function doJob()
    {
        Card::whereIn('id',$this->cardIds)->increment('commission', $this->args);
    }

    protected function getMessageInfo()
    {
        $this->message = ['name'=>'paycommisson','tag'=>'佣金','value'=>$this->args];
    }
}
