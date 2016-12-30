<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Wechat\Controller;
use App\Services\CardService;

use Illuminate\Support\Facades\Validator;
use Session;
use \Illuminate\Http\Request;
use App\Services\SmsService;

class MemberInfoController extends Controller
{
	public function __construct()
    {
        parent::__construct();
    }
	
	//会员信息页
	public function index()
	{
		return $this->theme_view('member_info');
	}

	//显示编辑手机号页面
	public function editPhone()
	{
		return $this->theme_view('member_info_phone');
	}

	//保存手机
	public function putPhone(Request $request)
	{
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
		unset($data['key']);
		$cardService = new CardService();
		$res = $cardService->saveMemberInfo($this->currentCard,$data);
		if(!$res){
			$message=[
					'info'=>'保存失败',
					'mobile'=>$data['mobile']
			];
			return redirect()->back()->with('message',$message);
		}
		return redirect('/wechat/member_info');
	}

//	保存姓名性别生日密码
	public function putMemberInfo(Request $request)
	{
		$data = $request->only(['mobile','key','name','sex','birthday','password']);
		$rules=[
			'mobile' => 'bail | regex:/^1[345678]{1}\d{9}$/',
			'key' => ['bail','required_with:mobile','digits:6'],
			'name' => 'bail | string | between:1,10',
			'sex' => 'bail | in:0,1,2',
			'birthday' => 'bail | date_format:Y/m/d',
			'password' => 'bail | numeric',
		];
		$messages = [
			'mobile.regex' => '手机号格式错误',
			'required_with' => '验证码必须为6位数字',
			'digits' => '验证码必须为6位数字',
			'string' => '名字不能超过十位',
			'between' => '名字不能超过十位',
			'sex' => '性别错误',
			'date_format' => '生日格式错误',
			'numeric' => '密码只能为数字',
		];
		$validator = Validator::make($data, $rules,$messages);
		if($validator->fails()){
			return response($validator->errors()->first(),401);
		}

		if($request->has('mobile')){
			if(!$this->mesVerify($request,$data['key'],$data['mobile'])){
				return response('验证码错误',401);
			}
			unset($data['key']);
		}

		foreach($data as $key=>$val){
			if($val === '' || $val === null){
				unset($data[$key]);
			}
		}

		$cardService = new CardService();
		$res = $cardService->saveMemberInfo($this->currentCard,$data);
		if(!$res){
			return response('保存失败',401);
		}
	}

//	显示口令编辑页
	public function editWord()
	{
		if (Session::has('ver_word_state') && session('ver_word_state') == "1" &&
				Session::has('ver_word_time') && session('ver_word_time') > time()) {
			$data = [
				'card_settings' => $this->settingService->get('card'),
				'card_info' => $this->currentCard,
			];
			return $this->theme_view('member_info_word', $data);
		} else {
			return redirect('wechat/member_info/verify');
		}
	}

//	保存口令
	public function putWord(Request $request)
	{
		Validator::extend('length', function ($attribute, $value, $parameters) {
			if (strlen($value) < $parameters[0]) {
				return false;
			} elseif (strlen($value) > $parameters[1]) {
				return false;
			} else {
				return true;
			}

		});
		$messages = [
				'word.length' => '口令不能多于15个字符',
		];
		$validator = Validator::make($request->all(),
			[
				'word' => 'length:1,15',
			],
			$messages
		);
		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator);
		}
		if($request->input('wordState')){
			$this->currentCard->pin = $request->input('word');
		}else{
			$this->currentCard->pin = '';
		}
		$this->currentCard->save();
		return redirect('wechat/member_info');
	}

//	显示口令验证页
	public function showVerify()
	{
		$data = [
				'card_settings' => $this->settingService->get('card'),
				'card_info' => $this->currentCard,
		];

		return $this->theme_view('member_info_word_ver', $data);
	}

//	验证口令
	public function verifyWord(Request $request)
	{
		$data = $request->all();
		Validator::extend('length', function ($attribute, $value, $parameters) {
			if (strlen($value) < $parameters[0]) {
				return false;
			} elseif (strlen($value) > $parameters[1]) {
				return false;
			}else{
				return true;
			}

		});
		$messages = [
			'word.length' => '口令不能多于15个字符',
			'key.digits_between' => '验证码为4到6位整数',
		];
		$validator = Validator::make($data,
			[
				'word' => 'sometimes | length:1,15',
				'key' => 'sometimes | digits_between:4,6'
			],
			$messages
		);
		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator);
		}
		if ($request->input('word')) {
			if ($request->input('word') == $this->currentCard->pin) {
				session(['ver_word_state' => 1]);
				session(['ver_word_time' => time() + 180]);
				return redirect('wechat/member_info/word');
			} else {
				return redirect('wechat/member_info/verify')->with('message','口令不正确');

			}
		} else {
			if(!$this->mesVerify($request,$data['key'],$this->currentCard->mobile,'word')){
				return redirect('wechat/member_info/verify')->with('message','验证码不正确');
			}
			session(['ver_word_state' => 1]);
			session(['ver_word_time' => time() + 180]);
			return redirect()->action('Wechat\MemberInfoController@editWord');
		}
	}

//	发送验证码
	public function sendSms(Request $request)
	{
		$data=$request->all();
		if(isset($data['mobile']) ){
			if(isset($data['type']) && $data['type']!='word'){
				if($this->currentCard->mobile==$data['mobile']){
					return response('未修改手机,不需要验证',401);
				}
			}
			$smsService=new SmsService();
			$res=$smsService->send($data['mobile'],$data['type']);
			if(!$res){
				return response('发送失败',401);
			}
		}else{
			return response('请输入手机号',401);
		}
	}

//	验证验证码
	private function mesVerify(Request $request,$key,$mobile,$type='')
	{
		if($type){
			$type .= '_';
		}
		$var_key=session($type.'ver_key','');
		$ver_mobile=session($type.'ver_mobile','');
		$ver_key_time=session($type.'ver_key_time','');
		$nowTime=time();
		if($key==$var_key && $mobile==$ver_mobile && $nowTime-$ver_key_time<180){
			$request->session()->forget($type.'ver_key');
			$request->session()->forget($type.'ver_mobile');
			$request->session()->forget($type.'ver_key_time');
			return true;
		}else{
			return false;
		}
	}

}