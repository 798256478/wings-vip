<?php

namespace App\Listeners;

use App\Events\CodeRedeemed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CodeRedeemedListener extends CardEventListener
{

    /**
     * Handle the event.
     *
     * @param  CodeRedeemed  $event
     * @return void
     */
    protected $message;
    protected $changes;
    public function handle(CodeRedeemed $event)
    {
       
        parent::handle($event);
    }
    
    protected function fillOperatingRecord($event, &$record)
    {
         $record->action= '兑换';
         $record->minimal='您使用兑换码'.$event->code;
         $record->event_type = 'CODE_REDEEMED';
         
        
        $record->data=['code'=>$event->code,'message'=>$this->message];//必须toarray();
        $record->summary = '您使用兑换码'.$event->code.'兑换了'.$this->message;
        $record->changes = $this->changes;
    }
    
    protected function conditionsMatching($event,$conditions=null)
    {
        return true;
    }
    
    protected function getTemplateMessageInfo($event, &$resourceArray)
    {
        $this->changes=array();
         if (isset($event->jobResult['SELF']) && isset($event->jobResult['SELF']['jobMessage'])) {
            $jobMessages = $event->jobResult['SELF']['jobMessage'];
            foreach ($jobMessages as $jobMessage) {
                $this->message .= $jobMessage['tag'].':';
                $this->message .= $jobMessage['value'];
                if (isset($jobMessage['count'])) {
                    $this->message .= ' * '.$jobMessage['count'];
                    $this->changes[$jobMessage['name']]=$jobMessage['count'];
                }
                else
                {
                     $this->changes[$jobMessage['name']]=$jobMessage['value'];
                }
                $this->message .= ",";
            }
        }
        
        $resourceArray['code']=$event->code;
        $resourceArray['message']=$this->message;
        return 'CODE_REDEEMED';
    }
    
}
