<?php

namespace App\Services\Health;

use App\Models\Health\Reservation;
use App\Exceptions\WingException;
use Illuminate\Support\Facades\DB;

class ReservationService
{
	public function addReservationInfo($data)
	{
		$reservation=new Reservation();
		$reservation->experiment_data_id=$data['experiment_data_id'];
		$reservation->name=$data['name'];
		$reservation->phone=$data['mobile'];
		$reservation->sex=$data['sex'];
		$reservation->time=date('Y-m-d H:i:s',strtotime($data['time']));
		$reservation->save();
		return $reservation->id;
	}

	public function getData($experiment_data_id){
		return $data=Reservation::where('experiment_data_id',$experiment_data_id)->get();
	}

	public function getReservationList($filter){
		$length=$filter['pageSize'];
		$query=Reservation::with('experimentData.barcode')->orderBy('time','desc')->skip($length * ($filter['nowPage'] - 1))->take($length);
		if($filter['filter']){
			return $query->where('name','like','%'.$filter['filter'].'%')->orWhereHas("experimentData",function($q) use ($filter){
				return $q->where('id',$filter['filter'].'%');
			})->get();
		}
		return $query->get();
	}


}