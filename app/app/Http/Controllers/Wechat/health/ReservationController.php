<?php

namespace App\Http\Controllers\Wechat\Health;

use App\Http\Controllers\Wechat\Controller;
use App\Services\Health\ReservationService;
use App\Services\Health\CustomerService;
use App\Services\Health\ProgressService;
use App\Models\Health\ExperimentData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ReservationController extends Controller
{
	public function __construct(ReservationService $reservationService)
	{
		parent::__construct();
		$this->reservationService=$reservationService;
	}

	public function showReservation($experiment_data_id)
	{
		$customerService = new CustomerService();
		$progressService = new ProgressService();
        $experiment_data=ExperimentData::find($experiment_data_id);
		$data['customers'] = $customerService->getUserBybarcodeid($experiment_data->barcode_id);
		$data['experiment_data'] = $experiment_data;
		return $this->theme_view('health/reservation', $data);
	}

	//保存用户信息
	public function postReservation(Request $request)
	{
		//			数据验证	
		$data['experiment_data_id'] = $request->input('experiment_data_id');
		$data['mobile'] = $request->input('mobile');
		$data['name'] = $request->input('name');
		$data['sex'] = $request->input('sex');
		$data['key'] = $request->input('ver_key');
		$data['time']=$request->input('time');
		$validator = $this->validator($data);
		if ($validator->fails()) {
			$message = '';
			foreach($validator->messages()->toArray() as $key){
				$message=$message.implode(',',$key).'  ';
			}
			return response($message, 401);
		}
		//验证码验证
		if ($data['key']) {
			if (!$this->mesVerify($data['key'], $data['mobile'])) {
				$message= '验证码错误';
				return response($message, 401);
			};
		}
		//限制预约次数
		$times=$this->reservationService->getData($data['experiment_data_id']);
		if (count($times) >= 3) {
			$messag = '您已经预约过3次，不能再预约了';
			return response($message, 401);
		}
		unset($data['key']);
		//保存数据
		$res = $this->reservationService->addReservationInfo($data);
		if (!$res) {
			$message = '信息保存失败';
			return response($message, 401);
		}
		return $res;
	}

	protected function validator($data)
	{
		$messages = [
				'mobile.regex' => '手机号格式不正确',
				'ver_key.digits_between' => '验证码为4到6位整数',
				'name.required' => '请输入姓名',
				'sex.required' => '请选择性别',
				'time.required'=>'请选择预约时间',
		];
		$validator = Validator::make($data,
			[
				'mobile' => 'regex:/^1[345678]{1}\d{9}$/',
				'ver_key' => 'digits_between:6,6',
				'name' => 'required',
				'sex' => 'required',
				'time'=>'required',
			],
			$messages
		);
		return $validator;
	}

	private function mesVerify($key, $mobile)
	{
		$var_key = session('ver_key', '');
		$ver_mobile = session('ver_mobile', '');
		$ver_key_time = session('ver_key_time', '');
		$nowTime = time();
		if($key == $var_key && $mobile == $ver_mobile && $nowTime - $ver_key_time < 180){
			return true;
		}else{
			return false;
		}
	}

}