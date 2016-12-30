<?php 

namespace App\Http\Controllers\Api\Yuda;

use App\Http\Controllers\Api\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Crypt;
use App\Services\Yuda\SyncService;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\Card;
use App\Services\JobService;
use Log;
class SystemController extends Controller
{
    public function __construct()
    {

    }

//    内部测试模拟接收
    public function receiveTest(Request $request)
    {
        $data = $request->getContent();
//        sleep(10);
//        dd($data);
        $data = json_decode($data,true);
        $sign = sha1(strtolower(config('customer.' . user_domain() . '.sync.key').$data['type'].$data['timestamp'].json_encode($data['data'],
                JSON_UNESCAPED_UNICODE)));
        if($sign == $data['sign']){
//            return json_encode([
//                'status'=>'FAIL',
//                'error_code'=>'错误测试',
//                'req'=>$data
//            ],JSON_UNESCAPED_UNICODE);
            return json_encode([
                'status'=>'SUCCEED',
                'req'=>$data
            ],JSON_UNESCAPED_UNICODE);
        }else{
            return json_encode([
                'status'=>'FAIL',
                'error_code'=>'签名错误',
                'req'=>$data
            ],JSON_UNESCAPED_UNICODE);
        }
    }

//    分发器
    public function distribute(Request $request)
    {
        $param = $request->getContent();
        $param = json_decode($param,true);
        $rules = [
            'type' => 'required | in:ORDER,GIFT',
            'timestamp' => 'required | integer',
            'data' => 'array',
            'sign' => 'required | string',
        ];
        $validator = app('validator')->make($param, $rules);
        if($validator->fails()){
            Log::info('同步日志记录格式验证错误',[$validator->errors()->all()]);
            return response(json_encode(['status'=>'FAIL', 'error_code'=>$validator->errors()->all(), 'req'=>$param],JSON_UNESCAPED_UNICODE),401);
        }
        $sign = sha1(strtolower(config('customer.' . user_domain() . '.sync.key').$param['type'].$param['timestamp'].json_encode($param['data'],
                JSON_UNESCAPED_UNICODE)));
        if($sign == $param['sign']){
//            return json_encode(['status'=>'SUCCEED', 'req'=>$param],JSON_UNESCAPED_UNICODE);
            switch ($param['type'])
            {
                case 'ORDER':
                    $orderService = new OrderService();
                    $res = $orderService->yudaOrderSyncToThis($param);
                    break;
                case 'GIFT':
                    $jobService = new JobService();
                    $res = $jobService->yudaGiftSyncToThis($param);
                    break;
                default:
                    return response(json_encode(['status'=>'FAIL', 'error_code'=>'类型错误', 'req'=>$param],JSON_UNESCAPED_UNICODE),401);
            }

            if($res === true){
                return json_encode(['status'=>'SUCCEED', 'req'=>$param],JSON_UNESCAPED_UNICODE);
            }else{
                Log::info('同步日志记录程序错误',[$res,$param]);
                return response(json_encode(['status'=>'FAIL', 'error_code'=>$res, 'req'=>$param],JSON_UNESCAPED_UNICODE),401);
            }

        }else{
            Log::info('同步日志记录签名错误',[$request->getContent()]);
            return response(json_encode(['status'=>'FAIL', 'error_code'=>'签名错误', 'req'=>$param],JSON_UNESCAPED_UNICODE),401);
        }
    }

//    密文测试
    public function test()
    {
        $text = '123456';
        $a = passwordEncrypt($text);
//        $b = $this->passwordDecrypt($a);
        echo '123456的密文:'.$a;
        echo '<br>';
        $b = $this->passwordDecrypt($a);
        echo $b;
    }

    //模拟发送post数据
    public function posttest()
    {
//        $data = [
//            'type'=>'CREATE',
//            'timestamp'=>time(),
//            'data'=>[
//                'code'=>'123456',
//                'mobile'=>'13939992839',
//                'nickname'=>'李广义',
//                'sex'=>1,
//                'birthday'=>'1382694957',
//                'password'=>'ABCDEFGHIJKLMN'
//            ]
//        ];
//        $data = [
//            'type'=>'GIFT',
//            'timestamp'=>time(),
//            'data'=>[
//                'id'=>'456',
//                'summary'=>'营销赠送测试',
//                'change_amount'=> -10,
//                'cards'=>[
//                    '18237123655'
//                ]
//            ]
//        ];
        $data = [
            'type'=>'ORDER',
            'timestamp'=>1482289442,
            'data'=>[
                'id'=>'b3a41537-b65f-4ca3-9209-f851b2bc31c6',
                'code'=>'35397783',
                'summary'=>'只积分',
                'total_fee'=>'249.60',
                'bonus_require'=>0,
                'bonus_present'=>249
            ]
        ];
        $syncService = new SyncService();
        $res = $syncService->sendDatatoYuda($data);
        dd($res);
    }

//    加密
    private function passwordEncrypt($str)
    {
    }

//    解密
    private function passwordDecrypt($str)
    {
        $str = base64_decode($str);
        $a = mcrypt_decrypt(MCRYPT_RIJNDAEL_256,config('customer.' . user_domain() . '.sync.mcrypt.key'),$str,MCRYPT_MODE_CBC,config('customer.' . user_domain() . '.sync.mcrypt.iv'));
        $a = $this->remove_cover($a);
        return $a;
    }

//    去除补位
    private function remove_cover($text)
    {
        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > 32) {
            $pad = 0;
        }
        return substr($text, 0, (strlen($text) - $pad));
    }

//    添加补位
    private function cover_position($text){

        $block_size = 32;
        $text_length = strlen($text);
        //计算需要填充的位数
        $amount_to_pad = $block_size - ($text_length % $block_size);
        if ($amount_to_pad == 0) {
            $amount_to_pad = $block_size;
        }
        //获得补位所用的字符
        $pad_chr = chr($amount_to_pad);
        $tmp = "";
        for ($index = 0; $index < $amount_to_pad; $index++) {
            $tmp .= $pad_chr;
        }
        return $text . $tmp;
    }

}