<?php

namespace App\Jobs;

use App\Jobs\CardJob;
use App\Models\Card;
use App\Services\CardService;
use App\Services\Yuda\SyncService;
use App\Exceptions\WingException;

class GiveBonus extends CardJob
{

    /**
     * Execute the job.
     *
     * @return void
     */
    protected function doJob()
    {
         Card::whereIn('id',$this->cardIds)->increment('bonus', $this->args);
        if(user_domain() === config('yuda.name')){
            $cardService =new CardService();
            $cards = $cardService->getCardByIdArray($this->cardIds);
            $cardIds = [];
            foreach($cards as $val){
                $cardIds[] = $val->id;
            }
            $data = [
                'type'=>'GIFT',
                'timestamp'=>time(),
                'data'=>[
                    'id'=>md5(time().mt_rand(0, 1000)),
                    'summary'=>'营销赠送',
                    'change_amount'=>$this->args,
                    'cards'=>$cardIds
                ]
            ];
            $syncService = new SyncService();
            $res = $syncService->sendDatatoYuda($data);
            if(!isset($res['status']) ||  $res['status'] !== 'SUCCEED'){
                throw new WingException('积分同步至裕达失败', 401);
            }
        }
    }
    protected function getMessageInfo()
    {
         $this->message = ['name'=>'bonus','tag'=>'积分','value'=>$this->args];
    }


}
