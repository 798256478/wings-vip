<?php

namespace App\Services;
use App\Jobs\CardJob;
use App\Models\Card;
use App\Models\OperatingRecord;

class JobService
{
      /**
     * 执行job
     * @param  cardIds  事件Id数组
     * @param  source
     *         ['type':'event,mass,code','reason':'消费满2000回馈','data':'']
     * @param  jobs  需要执行的job数组
     * @return result; 执行结果数组 $result=[
            'SELF'=>[
                'cards'=>[CardModel,CardModel],
                'jobMessage'=>[
                        ['name':'ticket','tag':'优惠券','value':'小拿铁','count':'2' ],
                        ['name':'bonus','tag':'积分','value':'100',]
                    ]
                ],
            'REFERRER'=>[
                'cards'=>[CardModel,CardModel],
                'jobMessage'=>[
                        ['name':'ticket','tag':'优惠券','value':'小拿铁','count':'2' ],
                        ['name':'bonus','tag':'积分','value':'100',]
                    ]
                ],
        ]
    */

    public function doJobs($cardIds,$source,$jobs)
    {
        $result = array();
        $messageGroup = array();
        foreach($jobs as $k=>$v){
            $messageGroup[$v['recipient']][] = $v;
        }
        foreach($messageGroup as $k=>$jobs){
             $jobMessages = array();
             $cards=$this->getRecipientCard($cardIds,$k);
             $job_cardid= array_column($cards->toArray(), 'id');
             foreach ($jobs as $job) {
                if(count($cards)>0){
                    $job = new $job['class']($job_cardid, $job['args']);
                    if($job->message != null){
                        array_push($jobMessages, $job->message);
                    }
                    $job->handle();
                }
            }
            if(count($jobMessages)>0){
                 $result[$k]=['cards'=>$cards,'jobMessage'=> $jobMessages];
            }
        }
        return $result;
    }

    protected function getRecipientCard($cardIds,$recipient){
        if($recipient == 'SELF'){
            return Card::whereIn('id',$cardIds)->get();
        }
        elseif($recipient == 'REFERRER'){
            return Card::whereIn('referrer_id',$cardIds)->get();
        }
        elseif($recipient == 'ROOT_REFERRER'){
            $referrer_cardIds=Card::whereIn('referrer_id',$cardIds)->select('id')->get();
            return Card::whereIn('referrer_id',$referrer_cardIds)->get();
        }
    }

    public function yudaGiftSyncToThis($param)
    {
        $rules = [
            'id' => 'required | string',
            'summary' => 'required | string',
            'change_amount' => 'required | integer',
            'cards' => 'required | array',
            'cards.*' => 'required | integer',
        ];
//        $message = [
//            'id' => '营销赠送编号错误',
//            'summary' => '营销赠送说明错误',
//            'change_amount' => '营销赠送积分错误',
//            'cards' => '营销赠送会员错误',
//        ];

        $validator = app('validator')->make($param['data'], $rules);
        if($validator->fails()){
            return $validator->errors()->all();
        }
        $jobs = [
            [
                'class' => 'App\Jobs\GiveBonus',
                'args' => $param['data']['change_amount'],
                'recipient' => 'SELF'
            ]
        ];
        $cardService = new CardService();
        $cards = $cardService->getCardByCodeArray($param['data']['cards']);
        $cardIds = [];
        foreach($cards as $val){
            $cardIds[] = $val->id;
        }
        $this->doJobs($cardIds,['type'=>'mass', 'reason'=>'裕达营销赠送'],$jobs);
        //记录日志
        $record = new OperatingRecord;
        $record->data = $jobs;
        $record->cards = $cardIds;
        $record->channel = 'yuda';
        $record->show_to_menber = false;
        $record->create_time = $param['timestamp'];
        $record->action = '营销赠送';
        $record->summary = $param['data']['summary'];
        $record->type = 'MASS';
        $record->changes = ['bonus'=>$param['data']['change_amount']];;
        $record->save();
        return true;
    }
}
