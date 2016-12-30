<?php

namespace App\Services;

use App\Models\Card;
use App\Models\Order;
use App\Models\Property;
use DB;
use App\Events\CardCreated;
use App\Services\Yuda\SyncService;

class CardService
{
    public function getCard($id)
    {
        return Card::find($id);
    }

    public function getTotal()
    {
        return Card::count();
    }

    public function getCardByOpenId($openId)
    {
        return Card::where('openid', $openId)
				->first();
    }

	public function createCardByWechatUser($data)
	{
        if(config('customer.' . user_domain() . '.sync')){
            DB::beginTransaction();
        }
		$card = new Card;
		$card->card_code = $this->getNewCode();
		$card->openid = $data['openid'];
		$card->nickname = $data['nickname'];
		$card->sex = $data['sex'];
        $card->level =1;
        $card->headimgurl = $data['headimgurl'];
		$card->save();
        if(config('customer.' . user_domain() . '.sync')){
            $data = [
                'type'=>'CREATE',
                'timestamp'=>time(),
                'data'=>[
                    'code'=>$card->card_code,
                    'nickname'=>$card->nickname,
                    'sex'=>$card->sex,
                ]
            ];
            $syncService = new SyncService();
            $res = $syncService->sendDatatoYuda($data);
            if($res['status'] === 'SUCCEED'){
                DB::commit();
            }else{
                DB::rollBack();
                return false;
            }
        }
        event(new CardCreated($card));
        return Card::find($card->id);//朱贝鸽修改，在事件中修改的属性不能显示
	}

    public function getCardSummaries()
    {
        return Card::select('id', 'card_code', 'nickname', 'mobile','level')->get();
    }

    /**
     * 获取新注册的200名用户
     * @method getNewCardSummaries
     * @return array
     */
    public function getNewCardSummaries(){
        return Card::select('id', 'card_code', 'nickname', 'created_at','level')->orderBy('created_at', 'desc')->take(200)->get();
    }

    public function getCardById($cardId){
        return Card::where('id', $cardId)->first();
    }

    public function getPast30Day($cardId){
        return Order::select(DB::raw('FORMAT(sum(total_fee),2) as cost, FORMAT(count(total_fee), 0) as arrive'))->where('card_id', $cardId)
            ->where('created_at', '>', date("Y-m-d",strtotime("-30 day")))->first();
    }

    public function getUsedTickets($cardId){
        return Card::with(['tickets' => function($query){
            $query->onlyTrashed();
        }])->where('id', $cardId)->get();
    }

    public function searchCardList($val){
        return Card::select('id', 'card_code', 'nickname', 'created_at','level')
            ->where('card_code', 'like', '%'.$val.'%')
            ->orWhere('mobile', 'like', '%'.$val.'%')
            ->get();
    }

    public function initCardData($data)
    {
        try{
            DB::beginTransaction();
            $card=Card::find($data['card_id']);
            if(strlen($data['level'])){
                 $card->level=$data['level'];
            }
            if(strlen($data['bonus'])){
                $card->bonus=$data['bonus'];
            }
            if(strlen($data['balance'])){
                $card->balance=$data['balance'];
             }
            $card->save();
            $propertyService=new   PropertyService;
            foreach ($data['properties'] as $model) {
                $d1=strtotime(date("Y-m-d"));
                $d2=strtotime($model['expiry_date']);
                $Days=round(($d2-$d1)/3600/24);
                $propertyService->AddProperty($card->id,$model['property_template_id'],$model['quantity'], $Days);
            }
            DB::commit();
            return true;
        }
        catch(\Exception $e){
           DB::rollback();
           return json_exception_response($e);
       }
    }

    private function getNewCode(){
        $day_int = (int)(date('z',time()));
        $day_str = sprintf("%03d", $day_int);
        if (strstr($day_str,'4')){
            $day_str = str_replace('4', '5', $day_str);
        }

        $number_int = mt_rand(0,99999);
        $number_str = sprintf("%05d", $number_int);
        if (strstr($number_str,'4')){
            $r_int = mt_rand(6,9);
            $r_str = (string)$r_int;
            $number_str = str_replace('4', $r_str, $number_str);
        }

        $code_str = $day_str . $number_str;

        $c = Card::where('card_code', $code_str)->count();

        if ($c > 0)
            return $this->get_new_code();
        else
            return $code_str;
    }

    public function addUserInfo($data){
        $card=new Card();
        $oneCard=$card->where('id',$data['card_id'])->first();
        if(isset($data['name'])){
            $oneCard->name=$data['name'];
        }
        if(isset($data['sex'])){
            $oneCard->sex=$data['sex'];
        }
        if(isset($data['mobile'])){
            $oneCard->mobile=$data['mobile'];
        }
        $res=$oneCard->save();
        return $res;
    }

    public function saveMemberInfo($card,$data)
    {
        $syncMember = [];
        if(config('customer.' . user_domain() . '.sync')){
//            DB::beginTransaction();
            $syncMember = [
                'code' => $card->card_code
            ];
            foreach($data as $key=>$val){
                if($key == 'password'){
                    $syncMember['password'] = passwordEncrypt($val);
//                    $data['$key'] = md5($val);
                }
                if($key == 'name'){
                    $syncMember['nickname'] = $val;
                }
                if($key == 'birthday'){
                    $syncMember['birthday'] = strtotime($val);
                }
                if($key == 'sex'){
                    $syncMember['sex'] = (int)$val;
                }
            }
        }


        foreach($data as $key=>$val){
            if($key == 'birthday'){
                $val = strtotime($val);
            }
            if($key == 'password'){
                $val = md5($val);
            }
            $card->$key = $val;
        }

        $card->save();
        if(config('customer.' . user_domain() . '.sync')){
            $syncData = [
                'type'=>'PROFILE_FILLED',
                'timestamp'=>time(),
                'data'=>$syncMember
            ];
            $syncService = new SyncService();
            $res = $syncService->sendDatatoYuda($syncData);
            if($res['status'] === 'SUCCEED'){
                DB::commit();
            }else{
                DB::rollBack();
                return false;
            }
        }
        return true;
    }

    public function getCardByCode($code)
    {
        return Card::where('card_code',$code)->first();
    }

    public function getCardByCodeArray($codes)
    {
        return Card::whereIn('card_code',$codes)->get();
    }

    public function getCardByIdArray($ids)
    {
        return Card::whereIn('id',$ids)->get();
    }


}
