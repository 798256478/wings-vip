<?php

namespace App\Jobs;

use App\Jobs\CardJob;
use App\Models\Card;

class GiveBalance extends CardJob
{

    /**
     * Execute the job.
     *
     * @return void
     */
    protected function doJob()
    {
        Card::whereIn('id',$this->cardIds)->increment('balance', $this->args);
    }
    protected function getMessageInfo()
    {
         $this->message = ['name'=>'balance','tag'=>'余额','value'=>$this->args];
    }

}
