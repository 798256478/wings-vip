<?php
namespace App\Http\Controllers\Wechat\Health;

use App\Http\Controllers\Wechat\Controller;
use App\Models\Card;
use App\Models\Health\Customer;
use App\Models\Health\Barcode;
use App\Services\Health\CustomerService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Services\CardService;
use App\Services\Health\BarcodeService;
use App\Services\SmsService;

class CustomerController extends Controller
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function addUserInfo()
	{
		$data = [
				'card_settings' => $this->settingService->get('card'),
				'card_info' => $this->currentCard,
		];
		return $this->theme_view('health/detection_user_info', $data);
	}

	//保存用户信息
	public function saveUserInfo(Request $request)
	{
		//			数据验证
		$data['barcode'] = $request->input('barcode');
		$data['mobile'] = $request->input('mobile');
		$data['name'] = $request->input('name');
		$data['sex'] = $request->input('sex');
		$data['key'] = $request->input('ver_key');
		$validator = $this->validator($data);
		if ($validator->fails()) {
			$message='';
			foreach($validator->messages()->toArray() as $key){
				$message=$message.implode(',',$key).'  ';
			}

			return response($message, 401);
		}
		//			验证码验证
		if (!$this->currentCard->mobile || $this->currentCard->mobile != $data['mobile']) {
			if (!$this->mesVerify($request,$data['key'], $data['mobile'])) {
				$message= '验证码错误';
				return response($message, 401);
			};
		}
		unset($data['key']);
		//条码验证
		$barcode=Barcode::with('customer')->where('code',$data['barcode'])->first();
		$barcodeService = new  BarcodeService;
        $message=$barcodeService->isBindingCustomer($barcode);
		if($message){
			return response($message, 401);
		}
		//保存数据
		$data['card_id'] = $this->currentCard->id;
		unset($data['barcode']);
		$data['barcode_id'] = $barcode->id;
		$customerService = new CustomerService();
		$res = $customerService->addUserInfo($data);
		if (!$res) {
			$message = '信息保存失败';
			return response($message, 401);
		}
		//是否同步会员
		if ($request->input('is-member')) {
			$cardService = new CardService();
			$res = $cardService->addUserInfo($data);
			if (!$res) {
				$message = '同步信息失败';
				return response($message, 401);
			}
		}
		return $barcode->id;
	}

	public function userInfo(Request $request)
	{
		$barcode_id = $request->route('barcode_id');
		$customerService = new CustomerService();
		$data = $customerService->getUserBybarcodeid($barcode_id);
		return $this->theme_view('health/detection_check_info', $data);
	}

	public function saveInfo(Request $request)
	{
		$id = $request->route('id');
		$data = $request->all();
		$customerService = new CustomerService();
		return $customerService->saveInfo($id,$data);
	}

	public function editPhone($id, $barcode){
		return $this->theme_view('health/detection_user_info_phone',['id'=>$id,'barcode'=>$barcode]);
	}

	public function putPhone(Request $request)
	{	
		$customerService = new CustomerService();

		$id = $request->input('id');
		$barcode = $request->input('barcode');
		$data['mobile'] = $request->input('mobile', '');
		$data['key'] = $request->input('key');
		$messages = [
			'mobile.regex' => '手机号格式不正确',
			'key.digits_between' => '验证码为4到6位整数',
		];
		$validator = Validator::make($data,
			[
				'mobile' => 'regex:/^1[345678]{1}\d{9}$/',
				'key' => 'digits_between:4,6'
			],
			$messages
		);
		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator);
		}
		if(!$this->mesVerify($request,$data['key'],$data['mobile'])){
			$message=[
				'info'=>'验证码错误或失效',
				'mobile'=>$data['mobile']
			];
			return redirect()->back()->with('message',$message);
		}

		if($customerService->saveInfo($id,['mobile'=>$data['mobile']])){
			return redirect('wechat/health/detection/' . $barcode . '/userInfo');
		}
	}

	protected function validator($data)
	{
		$messages = [
				'barcode.required' => '请输入条码',
				'mobile.regex' => '手机号格式不正确',
				'ver_key.digits_between' => '验证码为4到6位整数',
				'name.required' => '请输入姓名',
				'sex.required' => '请选择性别',
		];
			$validator = Validator::make($data,
				[
					'barcode' => 'required',
					'mobile' => 'regex:/^1[345678]{1}\d{9}$/',
					'ver_key' => 'digits_between:6,6',
					'name' => 'required',
					'sex' => 'required'
				],
				$messages
			);
		return $validator;
	}

	public function mesVerify(Request $request, $key, $mobile)
	{
		$var_key=session('ver_key','');
		$ver_mobile=session('ver_mobile','');
		$ver_key_time=session('ver_key_time','');
		$nowTime=time();
		if($key==$var_key && $mobile==$ver_mobile && $nowTime-$ver_key_time<180){
			return true;
		}else{
			return false;
		}
	}


	public function detectionSendSms(Request $request)
	{

		$data=$request->all();
		if(isset($data['mobile'])){
			if(isset($data['id'])){
				$customerService = new CustomerService();
				$customer = $customerService->getCustomer($data['id']);
				$originalMobile = $customer['mobile'];
			}else{
				$originalMobile = $this->currentCard->mobile;
			}
			if($originalMobile == $data['mobile']){
				return response('未修改手机,不需要验证',401);
			}
			$smsService=new SmsService();
			$res=$smsService->send($data['mobile'], $data['type']);
			if(!$res){
				return response('发送失败',401);
			}
		}else{
			return response('请输入手机号',401);
		}
	}
}

?>