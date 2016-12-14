<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


class OrderCompletedListener extends CardEventListener
{
    

    /**
     * Handle the event.
     *
     * @param  OrderCompleted  $event
     * @return void
     */
    protected $paymentStr='';
    protected $use_bonus=0;
    // protected $orderDetailsTitle;
    public function handle(OrderCompleted $event)
    {
        foreach ($event->order->orderPayments as  $model) {
            if($model->use_bonus>0)
            {
                $this->use_bonus=$model->use_bonus;
                $this->paymentStr = $this->paymentStr . $model->use_bonus;
            }
            $this->paymentStr = $this->paymentStr . $model->name . $model->amount . '元、';
        }
        if($event->order->bonus_pay_amount>0){
               $this->paymentStr = $this->paymentStr . '积分' . floor($event->order->bonus_pay_amount) . '、';//不知道为什么，这里总出现小数
        }
        if(mb_strlen($this->paymentStr) > 0)
            $this->paymentStr = mb_substr($this->paymentStr,0,mb_strlen($this->paymentStr)-1,'utf-8').'。';
        parent::handle($event);  
    }
    
    protected function fillOperatingRecord($event, &$record)
    {
        //TODO:添加记录内容
        $record->action = $event->order->body;
        $record->event_type =  $event->order->type;
        $record->data = ['order'=>$event->order->toArray()];//必须toarray();
        $record->minimal = $event->order->body.$event->order->money_pay_amount.'元'.$event->order->bonus_pay_amount.'积分。';
        $record->summary = '您'.$record->action.$event->order->total_fee.'元';
        if($event->order->money_pay_amount > 0){
             $record->summary = $record->summary.',实付'.$event->order->money_pay_amount.'元，';
        }
        if($event->order->bonus_pay_amount){
             $record->summary = $record->summary.$event->order->bonus_pay_amount.'积分。';
        }
        if($event->order->bonus_present > 0){
             $record->summary = $record->summary.'赠送您'.$event->order->bonus_present.'积分,';
        }
        if($event->order->bonus_present > 0){
             $record->summary = $record->summary.$event->order->balance_present.'余额。';
        }
        $record->summary = $record->summary.'付款方式：';
        
    
        $record->summary= $record->summary. $this->paymentStr;
       
        $record->changes = $this->getChanges($event);
        
    }

    
    
    protected function getChanges($event)
    {
        $changes=array();
        if($event->order->type != 'BALANCE'){
           $changes['consume'] = $event->order->money_pay_amount;
        }
        else{
            $changes['balance'] = $event->order->balance_fee+$event->order->balance_present; 
        }
        foreach ($event->order->orderPayments as  $model) {
            if($model->type == 'BALANCE'){
                if(isset($changes['balance'])){
                    $changes['balance']=$changes['balance']-$model->amount;
                }
                else{
                    $changes['balance']=0 - $model->amount;
                }
                break;
            }
        }
        if($event->order->ticket_id != null){
            $changes['ticket'] = -1;
        }
        $changes['bonus'] =floor($event->order->bonus_present-$event->order->bonus_require-$this->use_bonus);

        if($event->order->type == 'GOODS'){
            foreach ($event->order->orderDetails as $model) {
//                $changes['ticket'] = $model->commoditySpecificationHistory->id;
                $this->getGoodChanges($model,$changes);
            }
        }
        return $changes;
    }
    
    private function getGoodChanges($model, &$changes)
    {
        if($model->commoditySpecificationHistory->sellable_type == 'App\Models\TicketTemplate'){
            $changes['ticket'] = (isset($changes['ticket']) ? $changes['ticket'] : 0) + $model->amount;
        }
        if($model->commoditySpecificationHistory->sellable_type == 'App\Models\PropertyTemplate'){
            $changes['property'] = (isset($changes['property']) ? $changes['property'] : 0) + $model->amount;
        }
    }
    
    protected function conditionsMatching($event,$conditions=null)
    {
        if($event->order->type == 'BALANCE')
            return false;
        foreach ($conditions as $key => $value) {
           if(!$this->matchOnce($event,$key,$value)){
               return false;
           }
        }
        return true;
    }
    
    private function matchOnce($event,$key,$value)
    {
        $min=0;$max=2147483647;
        if($value!=null){
            if(isset($value['min'])&&$value['min']!=null){
                 $min=$value['min'];
            }
             if(isset($value['max'])&&$value['max']!=null){
                 $max=$value['max'];
            }
        }
        //初次消费
        if($key == 'FIRST'&&$event->card->total_expense-$event->order->money_pay_amount == 0){
                return true; 
        }
        //总消费
        if($key == 'TOTAL_EXPENSE'){
            if($event->card->total_expense >= $min&&$event->card->total_expense-$event->order->money_pay_amount < $min
             && $event->card->total_expense<$max)
                 return true;
        }
        //单次消费
        if($key == 'SINGLE_EXPENSE'){
            if($event->order->money_pay_amount >= $min && $event->order->money_pay_amount < $max)
                  return true;
        }
        //累计消费次数
        if($key == 'EXPENSE_COUNT'){
            if($event->card->total_visit == $value) 
                return true;
        }
        return false;
    }
    
    protected function getTemplateMessageInfo($event, &$resourceArray)
    {
        $resourceArray['order'] = $event->order->toArray();
        if($event->order->type == 'GOODS'){
            $title='';
            foreach ($event->order->orderDetails as $model) {
                $title = $title. $model->amount.'个'.
                $model->commoditySpecificationHistory->commodityHistory->name.$model->commoditySpecificationHistory->name.',';
            }
            if(strlen($title) > 0)
                $title = substr($title,0,strlen($title)-1).'。';
            $resourceArray['title']=$title;
            $resourceArray['paymentStr']=$this->paymentStr;
        }
        return $event->order->type;
    }
}
