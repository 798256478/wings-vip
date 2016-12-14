<?php

namespace App\Services;

use App;
use App\Models\Refund;
use App\Models\Order;
use App\Models\OrderDetail;
use DB;

class RefundService
{
	public function createRefund($data){
		$refund = new Refund();
		$refund->order_id = $data['order_id'];
		$refund->order_detail_id = $data['order_detail_id']?$data['order_detail_id']:null;
		$refund->type = $data['order_detail_id']?'GOODS':'ORDER';
		$refund->name = $data['name'];
		$refund->phone = $data['phone'];
		$refund->state = 'APPLY';
		$refund->reason = $data['reason'];
		$refund->save();
	}

	public function getRefundsData($data){
		$length=$data['page_size'];
		$query=Refund::with('order.card', 'orderDetail.commoditySpecificationHistory.commodityHistory', 'order.orderDetails.commoditySpecificationHistory.commodityHistory');
		if(isset($data['state'])&&$data['state']!='ALL'){
			$query=$query->where('state',$data['state']);
		}
		if(isset($data['start'])){
			$query = $query->where('created_at', '>', date("Y-m-d H:i:s",strtotime($data['start'])));
		}
		if(isset($data['end'])){
			$query= $query->where('created_at', '<', date("Y-m-d  H:i:s",strtotime('+1 day',strtotime($data['end']))));
		}
		if(isset($data['number'])&&$data['number']){
			$query=$query->whereHas("order",function($q) use ($data){
				return $q->where('number','like',$data['number'].'%');
			});
		}
		if(isset($data['buyer'])&&$data['buyer']){
			$query=$query->whereHas("order.card",function($q) use ($data){
				return $q->where('nickname','like','%'.$data['buyer'].'%');
			});
		}
		$RefundData['total']=count($query->get()->toArray());
		if($data['page']>0){
			$RefundData['data']=$query->skip($length*($data['page'] - 1))->take($length)->orderBy('created_at','desc')->get();
			return $RefundData;
		}else{
			 throw new \Dingo\Api\Exception\StoreResourceFailedException('页码错误', ['page' => ['页码必须大于0']]);
		}
	}

	public function getRefundData($id){
		return Refund::with(
			'order.card',
			'orderDetail.commoditySpecificationHistory.commodityHistory', 
			'order.orderDetails.commoditySpecificationHistory.commodityHistory',
			'orderDetail.commoditySpecificationHistory.suiteChildHistories.commodityHistory',
			'order.orderDetails.commoditySpecificationHistory.suiteChildHistories.commodityHistory',
			'order.orderPayments',
			'order.ticket.ticketTemplate'
			)->find($id);
	}

	public function dealRefund($data){
		$refund=Refund::with('orderDetail')->find($data['id']);	
		$refund->state=$data['state'];
		$refund->money=$data['money'];
		$refund->is_refund_other=$data['is_refund_other'];
		$refund->instructions=$data['instructions'];
		$refund->hand_time=date('Y-m-d H:i:s',time());
		$refund->save();
		return $refund->id;
	}

	public function cancelRefund($id){
		$refund = Refund::with('orderDetail')->find($id);	
		$refund->hand_time=date('Y-m-d H:i:s',time());
		$refund->state='CLOSE';
		$refund->save();
		if($refund->type === "ORDER"){
			$order = Order::find($refund->order_id);
			foreach ($order->orderDetails as $key => $value) {
				$orderDetail=OrderDetail::find($value->id);
				$orderDetail->is_refund=false;
				$orderDetail->save();
			}
			return $orderDetail->id;
		} else{
			$orderDetail=OrderDetail::find($refund->order_detail_id);
			$orderDetail->is_refund=false;
			$orderDetail->save();
			return $orderDetail->id;
		}	
	}
}