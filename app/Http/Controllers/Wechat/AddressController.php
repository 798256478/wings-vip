<?php

namespace App\Http\Controllers\Wechat;

use Illuminate\Http\Request;
use App\Services\AddressService;
use App\Exceptions\WingException;

class AddressController extends Controller
{
	protected $addressService;
	public function __construct(AddressService $addressService)
	{
		parent::__construct();
		$this->addressService = $addressService;
	}

//	显示地址列表
	public function showAddresses()
	{
		$data['address'] = $this->addressService->getAddressList($this->currentCard->id);
		return $this->theme_view('shop.address',$data);
	}

	//	显示新建地址页                                                                            m
	public function newAddress()
	{
		$defaultAddress=$this->addressService->getDefaultAddressStringByCardId($this->currentCard->id);
		if(!$defaultAddress){
			$data['isNeedDefault']=true;
		}
		$data['title'] = '新建收货地址';
		return $this->theme_view('shop.edit_address',$data);
	}

//	保存新建地址
	public function postAddress(Request $request)
	{
		$data = $request->all();
		try {
			$address = $this->checkData($data);
		}catch (\Exception $e) {
			return json_exception_response($e);
		}
		$this->addressService->addAddress($address);
	}

//	显示编辑地址页
	public function editAddress($id)
	{
		$data['address'] = $this->addressService->getAddress($this->currentCard->id,$id);
		$data['title'] = '编辑收货地址';
		return $this->theme_view('shop.edit_address',$data);
	}

//	保存编辑地址
	public function putAddress(Request $request,$id)
	{
		$data = $request->all();
		try {
			$address = $this->checkData($data);
		}catch (\Exception $e) {
			return json_exception_response($e);
		}
		$this->addressService->editAddress($this->currentCard->id,$id,$address);
	}

//	删除地址
	public function deletedAddress($id)
	{
		$this->addressService->deleteAddress($this->currentCard->id,$id);
	}

//	验证数据并收集数据
	private function checkData($data)
	{
		if(!isset($data['name']) || empty($data['name'])){
			throw new WingException('请输入收货人姓名', 401);
		}
		if(!isset($data['tel']) || empty($data['tel'])){
			throw new WingException('请输入收货人联系电话', 401);
		}
		if(!preg_match('/^1[345678]\d{9}/',$data['tel'])){
			throw new WingException('联系电话格式不正确', 401);
		}
		if(!isset($data['detail']) || empty($data['detail'])){
			throw new WingException('请输入详细地址', 401);
		}
		if(!is_array($data['area'] )){
			throw new WingException('请选择所在地区', 401);
		}
		$address['name'] = $data['name'];
		$address['tel'] = $data['tel'];
		$address['province'] = $data['area'][0];
		$address['city'] = $data['area'][1];
		$address['area'] = $data['area'][2];
		$address['detail'] = $data['detail'];
		$address['isdefault'] = $data['isdefault']=='true'?1:0;
		$address['card_id'] = $this->currentCard->id;
		return $address;
	}

}
