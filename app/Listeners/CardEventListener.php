<?php

namespace App\Listeners;


use Config;
use Session;
use App;
use App\Services\TemplateMessageService;
use App\Services\AuthService;
use App\Services\EventRuleService;
use App\Services\JobService;
use App\Models\OperatingRecord;
use App\Events\CardEvent;
use App\Models\EventExceptionRecord;


abstract class CardEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(AuthService $authService,TemplateMessageService $templateMessageService)
    {
        $this->templateMessageService = $templateMessageService;
        $this->authService = $authService;
        $this->recipient_options = Config::get('cardevent.recipient_options');
        $this->job_results=array();
    }

    protected $templateMessageService;
    protected $authService;
    protected $recipient_options;

    /**
     * 事件处理句柄
     *
     * @param  CardEvent  $event
     * @return void
     */
    public function handle(CardEvent $event)
    {
        try {
            $this->tryToDoJob($event);
            $this->sendTemplateMessage($event);
            $record = $this->getNewEventRecord($event);
            $this->fillOperatingRecord($event, $record);
            $record->save();
        }catch (\Exception $e) {
//            TODO 错误日志记录
            $message = method_exists($e, 'getMessage') ? $e->getMessage() : 'could_not_login';
            $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
            $errors = method_exists($e, 'getErrors') ? $e->getErrors() : [];
            $eventExceptionRecord = new EventExceptionRecord();
            $eventExceptionRecord->message = $message;
            $eventExceptionRecord->statusCode = $statusCode;
            $eventExceptionRecord->errors = $errors;
            $eventExceptionRecord->event = $event;
            $eventExceptionRecord->create_time = time();
            $eventExceptionRecord->save();

        }
    }

    /**
     * 填充操作记录（虚方法，需重写）
     * @param  CardEvent $event  事件对象
     * @param  Array $record 记录内容（引用传递）
     * @return null
     */
    abstract protected function fillOperatingRecord($event, &$record);

    /**
     * 获取模板消息信息（虚方法，需重写）
     * @param  CardEvent $event         事件对象
     * @param  Array $resourceArray 用于模板消息的数据数组，可在配置中使用
     * @return string                事件配置Key
     */
    abstract protected function getTemplateMessageInfo($event, &$resourceArray);

    /**
     * 判断条件是否满足（虚方法，需重写）
     * @param  CardEvent $event         事件对象
     * @param  string $conditions 条件K，在配置文件中
     * @return boolval               是否满足条件
     */
    abstract protected function conditionsMatching($event,$conditions= null);

    protected function getNewEventRecord($event)
    {
        $record = new OperatingRecord;
        $record->event_class = get_class($event);
        $record->cards = [$event->card->id];
        $record->card = $event->card->toArray();
        $record->type ='event';
        $record->show_to_member=true;
        $this->getRecordOperator($record);
        $record->create_time=time();
        return $record;
    } 
    
    //发送事件模板消息
    protected function sendTemplateMessage($event)
    {
        $resourceArray = [
            'card' => $event->card->toArray(),
        ];

        $key = $this->getTemplateMessageInfo($event, $resourceArray);
        $this->templateMessageService->send($key, $event->card, $resourceArray);
    }
    
    //执行job
    protected function tryToDoJob(CardEvent $event)
    {
        $eventRuleService=new EventRuleService;
        $eventRules = $eventRuleService->getEventRulesByEventClass(get_class($event));
        $jobService = new JobService;
        foreach ($eventRules as $rule){
            if($this->conditionsMatching($event,$rule->conditions)&&count($rule->jobs)>0){
                $result=$jobService->doJobs([$event->card->id],['type'=>'event','reason'=>$rule->title],$rule->jobs);
                if(count($this->job_results)==0)
                {
                    $this->job_results=$result;
                }
                else{
                      if(isset($this->job_results['SELF'])){
                           array_push($this->job_results['SELF']['jobMessage'],$result['SELF']['jobMessage']);
                      }
                      if(isset($this->job_results['REFERRER'])){
                          array_push($this->job_results['REFERRER']['jobMessage'],$result['REFERRER']['jobMessage']);
                      }
                }
                $this->sendJobTemplateMessage($rule->title,$result);
            }
        }
    }
    
    //发送job模板消息，一个规则发一条
    protected function sendJobTemplateMessage($reason,$result)
    {
        foreach ($result as $key=>$model) {//分类，自己或者推荐人
            $message='';//发送的消息
            $changes=array();//变更的值
            foreach ($model['jobMessage'] as $item) {
                $change_key=$item['name'];
                $change_value=0;

                if(isset($item['count'])){
                    $message = $message.$item['count'].'张'.$item['value'].$item['tag'].',';
                    $change_value=$item['count'];
                }
                else{
                    $message = $message.$item['value'].$item['tag'].','; 
                    $change_value=$item['value'];
                }
                if(isset($changes[$change_key])){
                    $changes[$change_key] += $change_value;
                }
                else{
                      $changes[$change_key] = $change_value;
                }
            }
            if(strlen($message) > 0)
                $message = substr($message,0,strlen($message)-1).'。';
            $card = $model['cards'][0]->toArray();
            $reason = $this->recipient_options[$key].$reason;
            $resourceArray = [
                'card' => $card ,
                'reason'=>$reason,
                'message'=>$message,
            ];
            $this->templateMessageService->send('GIVE_GIFT', $model['cards'][0], $resourceArray);     
            $this->sendRuleRecord($card,$changes, $reason,$message);
        }
    }
    
    //记录job的日志，一个规则记录一条
    protected function sendRuleRecord($card,$changes, $reason,$message)
    {
        $record = new OperatingRecord;
        $record->type = 'job';
        $record->create_time = time();
        $record->card = $card;
        $record->cards = [$card['id']];
        $record->summary = $reason.','.$message;
        $record->show_to_member = true;
        $record->action = '获赠礼包';
        $record->minimal = $reason.','.'获赠礼包';
        $this->getRecordOperator($record);
        $record->changes = $changes;
        $record->save();
    }
    
    
    //获取
    protected function getRecordOperator(&$record)
    {
        $operator = null;
        try {
            $operator = $this->authService->getAuthenticatedUser()->toArray();
            $record->channel='shop';
        } catch(\Exception $e) {
            if (Session::has('wechat_user')){
                $operator = [
                    'display_name' => '微信用户',
                    'roles' => 'wechat'
                ];
                $record->channel='wechat';
            }
        }
        $record->operator = $operator;
    }

}
