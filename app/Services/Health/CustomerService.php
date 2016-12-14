<?php

namespace App\Services\Health;

use App\Models\Health\Customer;
use App\Models\Health\Barcode;
use App\Services\Health\BarcodeService;
use App\Exceptions\WingException;
use Illuminate\Support\Facades\DB;



class CustomerService
{
//	得到客户总数
	public function getCustomerTotal($name)
	{
		if($name==='0') {
			return Customer::count();
		}else{
			return Customer::where('name','like','%'.$name.'%')->count();
		}
	}

	//得到当前页列表
	public function getCustomerList($page,$name)
	{
		$length=15;
		if($name==='0') {
			return Customer::with('barcode')
					->orderBy('created_at', 'desc')
					->skip($length * ($page - 1))
					->take($length)
					->get();
		}else{
			return Customer::with('barcode')
					->where('name','like','%'.$name.'%')
					->orderBy('created_at', 'desc')
					->skip($length * ($page - 1))
					->take($length)
					->get();
		}
	}

	public function addUserInfo($data)
	{
		$customer=new Customer();
		$oneCustomer=$customer->where('card_id',$data['card_id'])->where('barcode_id',$data['barcode_id'])->first();
		if($oneCustomer){
			foreach($data as $key=>$val){
				if($key!='barcode' || $key!='ver_key'){
					$oneCustomer->$key=$val;
				}
			}
			$res=$oneCustomer->save();
		}else{
			foreach($data as $key=>$val) {
				if ($key != 'ver_key') {
					$customer->$key = $val;
				}
			}
			$res=$customer->save();
		}
		return $res;
	}


	public function getUserBybarcodeid($barcode_id)
    {
        $customer=Customer::where('barcode_id',$barcode_id)->first();
        $customer['code']=Barcode::find($customer->barcode_id)->code;
        return $customer;
    }

	public function saveInfo($id,$data){
        $User = Customer::find($id);
        foreach ($data as $key => $value) {
            $User[$key] = $data[$key];
        }
        $User->save();
        return $id;
    }

	public function addHealthCustomer($data)
	{
//		如果输入条码,判断条码是否存在并是否能够使用
		if(isset($data['code'])){
			$barcodeService=new BarcodeService();
            $barcode = Barcode::where('code',$data['code'])->first();
			if(!$barcode){
                throw new WingException($result, 401);
            }
			$a['barcode_id']=$barcode->id;
		}
		foreach($data as $key=>$val){
			if($key!='code'){
				$a[$key]=$val;
			}
		}
		$res=Customer::insert($a);
		if(!$res){
			throw new WingException("新增客户失败", 401);
		}
		return '新增客户成功';
	}

	public function editCustomer($data)
	{
		$id=$data['id'];
        $customer=Customer::find($id);
		if($data['code']){
			$barcodeService=new BarcodeService();
            $barcode = Barcode::where('code',$data['code'])->first();
            if(!$barcode){
                throw new WingException('条码不存在', 401);
            }
            if($customer->barcode_id != $barcode->id && $barcode->customer){
                 throw new WingException('条码已使用', 401);
            }
			$data['barcode_id']=$barcode->id;
		}else{
            $data['barcode_id'] = null;
		}
		unset($data['code']);
		unset($data['id']);
		$res = $customer->update($data);
		if(!$res){
			throw new WingException("修改资料失败", 401);
		}
		return '修改资料成功';
	}

	public function getCustomer($id){
		return Customer::find($id);
	}

}