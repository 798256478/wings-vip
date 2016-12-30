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
        if(config('customer.' . user_domain() . '.sync')){
            $cardService = new CardService();
            $cards = $cardService->getCardByIdArray($this->cardIds);
            $cardIds = [];
            foreach($cards as $val){
                $cardIds[] = "$val->card_code";
            }
            $data = [
                'type'=>'ORDER',
                'timestamp'=>time(),
                'data'=>[
                    'id'=>getGuid(),
                    'cards'=>$cardIds,
                    'summary'=>'营销赠送',
                    'change_amount'=>$this->args
                ]
            ];
            $syncService = new SyncService();
            $res = $syncService->sendDatatoYuda($data);
            if(!isset($res['status']) ||  $res['status'] !== 'SUCCEED'){
                throw new WingException(json_encode($res,JSON_UNESCAPED_UNICODE), 401);
            }
        }
    }
    protected function getMessageInfo()
    {
         $this->message = ['name'=>'bonus','tag'=>'积分','value'=>$this->args];
    }


}
