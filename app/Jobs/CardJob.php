<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Services\TemplateMessageService;
use App\Models\OperatingRecord;
use App\Services\AuthService;
use App\Models\Card;

abstract class CardJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    

    // public $source;
    public $args;
    public $cardIds;
    public $authService;
    public $message;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($cardIds=null,$args = null)
    {
        $this->args = $args;
        $this->cardIds = $cardIds;
        $this->getMessageInfo();
    }

    public function handle()
    {
        $this->doJob();
    }

    /**
     * 执行Job（虚方法，需重写）
     * @return null
     */
    abstract protected function doJob();



    abstract protected function getMessageInfo();



    
}
