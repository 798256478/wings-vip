<?php

namespace App\Listeners;

use App\Events\SignEvent;
use App\Models\Sign;
use App\Service\SignService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SignListener extends CardEventListener
{

    protected $pre_sign;
    protected $days=1;
    protected $bonus=0;
    protected $message='';
    public function handle(SignEvent $event)
    {
         //  $signService  = new  SignService;
         //  $this->pre_sign=$signService->getPreSign($event->card->id);

        $this->pre_sign = Sign::where('card_id',$event->card->id)->orderby('created_at', 'desc')->first();
        if($this->pre_sign){
            $date=date('Y-m-d',strtotime($this->pre_sign->created_at));//created_at是个object类型
            if($date==date("Y-m-d",strtotime("-1 day"))){
                $this->days = $this->pre_sign->days+1; 
            }
            // if($date==date("Y-m-d")){
            //     $this->days = $this->pre_sign->days+1; 
            // }
        }
        parent::handle($event);
        $sign=new Sign;
        $sign->card_id = $event->card->id;
        $sign->bonus = $this->bonus;
        $sign->days = $this->days;
        $sign->save();
    }
    
    protected function fillOperatingRecord($event, &$record)
    {
         $record->summary = '您于'.time().'签到，连续'.$this->days.'天,赠送您'.$this->message;
         $record->action='签到';
         $record->data=[];
         $record->minimal='签到';
         $record->event_type = 'SIGN';
    }
    
    protected function conditionsMatching($event,$conditions=null)
    {
        foreach ($conditions as $key => $value) {
            $min=0;$max=2147483647;
            if(isset($value['min'])&&$value['min']!=null){
                $min=$value['min'];
            }
            if($this->days < $min)
            {
                return false;
            }
            if(isset($value['max'])&&$value['max']!=null){
                $min=$value['max'];
            }
            if($this->days >= $max){
                return false;
            }
            
        }
        return true;
    }
    
    
    protected function getTemplateMessageInfo($event, &$resourceArray)
    {
        if(isset($this->job_results['SELF']) && isset($this->job_results['SELF']['jobMessage'])){
            foreach ($this->job_results['SELF']['jobMessage'] as $jobMessage) {
                if(isset($jobMessage[0])){
                    $jobMessage=$jobMessage[0];
                }
                $this->message .= $jobMessage['tag'].':';
                $this->message .= $jobMessage['value'];
                if (isset($jobMessage['count'])) {
                    $this->message .= ' * '.$jobMessage['count'];
                    $changes[$jobMessage['name']]=$jobMessage['count'];
                }
                else
                {
                    $changes[$jobMessage['name']]=$jobMessage['value'];
                }
                if($jobMessage['name']=='bonus'){
                    $this->bonus = $jobMessage['value'];
                }
                $this->message .= ",";
            }
        }
    }
}
