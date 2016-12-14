<?php

namespace App\Services;
use DB;
use App\Models\OperatingRecord;

class OperatingRecordService
{
    public function getListByAction($val)
    {
        return OperatingRecord::where('action', $val)->orderBy('created_at', 'desc')->take(5)->get();
    }

    public function getByConditions($conditions){

        $search= OperatingRecord::where('show_to_member',$conditions['show_to_member']);
        if(isset($conditions['card_id'])&&strlen($conditions['card_id'])>0)
             $search->where('cards',intval($conditions['card_id']));
        return $search->orderBy('created_at', 'desc')->take(30)->get();
    }

    public function statistical($user)
    {
        $userid= $user->id;
        $result=array('consume'=>0,'goods_consume'=>0,'balance'=>0,'ticket_count'=>0,'ticket_verified_count'=>0,'to_store'=>0,'today_card_count'=>0);
        $result=DB::connection('mongodb')->collection('operating_records')->raw(function($collection) use( $userid,$result)
        {
            $query=array('operator.id' => $userid, 'show_to_member' => TRUE,'create_time'=>['$gte'=>strtotime(date("Y-m-d"))]);
            $card=array();
            $to_store_array=iterator_to_array($collection->find($query),false);

            $query['event_type']='CONSUME';
            $consume_array=iterator_to_array($collection->find($query),false);
            $query['event_type']='BALANCE';
            $balance_array=iterator_to_array($collection->find($query),false);
            $query['event_type']='GOODS';
            $goods_array=iterator_to_array($collection->find($query),false);

             foreach ($to_store_array as $model) {
                $cards=$model['cards'];
                foreach ($cards as $id) {
                    if(!in_array($id,$card)){
                        array_push( $card,$id);
                    }
                }
            }
            $result['to_store']=count($card);
            foreach ($consume_array as $model) {
                $result['consume']=$result['consume']+abs($model['changes']['consume']);
            }
            foreach ($balance_array as $model) {
                $result['balance']=$result['balance']+$model['changes']['balance'];
            }

            foreach ($goods_array as $model) {
                $result['goods_consume']=$result['goods_consume']+$model['changes']['consume'];
            }
            $query['event_type']='TicketVerified';
            $result['ticket_verified_count']=count(iterator_to_array($collection->find($query)));
            //$result['today_card_count']=$this->card_model->get_today_card_count();//新会员

            return $result;
        });
         return $result;

    }

    //根据card_code查询所有交易记录
    public function GetOperatingRecordsByCardCode($card_code)
    {
        $query=array('card.card_code' => $card_code, 'show_to_member' => true);
        $result=OperatingRecord::where($query)->orderBy('created_at', 'desc')->take(30)->get();
        return $result;

    }

    //根据card_code查询积分兑换记录
    public function getBonusExchange($card_code)
    {
        $query=array('card.card_code' => $card_code, 'show_to_member' => true);
        $result=OperatingRecord::where($query)->where('changes.bonus','<',0)
            ->orderBy('created_at', 'desc')->take(30)->get();
        return $result;

    }

    //根据card_code查询积分明细
    public function getBonusList($card_code)
    {
        $query=array('card.card_code' => $card_code, 'show_to_member' => true);
        $result=OperatingRecord::where($query)->where('changes.bonus','>',0)
            ->orderBy('created_at', 'desc')->take(30)->get();
        return $result;

    }

    public function getOperatingList($page,$search)
    {
        if((int)$page>0) {
            $length = 14;
            $query = $this->assembleSql($search);
            $data=$query->orderBy('created_at', 'desc')->skip($length * ($page - 1))->take($length)->get();
            $data=$data->toArray();
            foreach($data as &$a){
                if(isset($a['changes'])) {
                    foreach ($a['changes'] as $key => &$val) {
                        if ($key == 'payCommission' || $key == 'balance') {
                            $val = number_format($val, 2, ".", ",");
                        }
                        if ($val != 0 && substr($val, 0, 1) != '-') {
                            $val = '+' . $val;
                        }
                    }
                }
            }
            return $data;
        }else{
            throw new \Dingo\Api\Exception\StoreResourceFailedException('页码错误', ['page' => ['页码必须大于0']]);
        }
    }

    public function getTotal($search)
    {
        $query = $this->assembleSql($search);
        return $query->count();
    }

    //拼凑SQL
    private function assembleSql($search)
    {
        $query=new OperatingRecord();
        if(isset($search['cardCode'])&&strlen($search['cardCode'])>0){
            $query=$query->where('card.card_code','like','%'.$search['cardCode'].'%');
        }
        if(isset($search['action'])&&strlen($search['action'])>0) {
            $query = $query->where('action','like','%'.$search['action'].'%');
        }
        return $query;
    }
}
