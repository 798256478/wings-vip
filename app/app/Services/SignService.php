<?php

namespace App\Services;


use DB;
use App\Models\Sign;

class SignService
{
	public function getTodaySignData($id){
		return Sign::where('card_id',$id)->where('created_at','>',date('Y-m-d').' 00:00:00')->get();
	}

	public function getSignData($id){
		$beginTime=date('Y-m-d',strtotime('-1 days',strtotime(date('Y-m-d').' 00:00:00')));
		$signData['data']=Sign::select('days','created_at')->where('card_id',$id)->where('created_at','>',$beginTime.' 00:00:00')->orderBy('created_at','desc')->first();
		if($signData['data']){
			$signData['days']=$signData['data']['days'];
			$beginTime=date('Y-m-d',strtotime($signData['data']['created_at']));
			$days=intval($signData['data']['days'])-1;
			$endtime = date('Y-m-d',strtotime('-'.$days.' days',strtotime($beginTime.' 00:00:00')));
			$signData['data']=Sign::where('card_id',$id)->where('created_at','>',$endtime.' 00:00:00')->get();
		}else{
			$signData['days']=0;
		}
		return $signData;
	}
}

