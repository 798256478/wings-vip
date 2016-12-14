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
class SystemController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {

        $data = $request->getContent();
//        dd($data);
        $data = json_decode($data,true);
        $sign = sha1(strtolower(config('yuda.key').$data['type'].$data['timestamp'].$data['data']));
        if($sign == $data['sign']){
            return json_encode([
                'status'=>'SUCCEED',
                'req'=>$data
            ],JSON_UNESCAPED_UNICODE);
        }else{
            return json_encode([
                'status'=>'FAIL',
                'error_code'=>'',
                'req'=>$data
            ],JSON_UNESCAPED_UNICODE);
        }
    }

//    内部测试模拟接收
    public function receiveTest(Request $request)
    {
        $data = $request->getContent();
//        sleep(10);
//        dd($data);
        $data = json_decode($data,true);
        $sign = sha1(strtolower(config('yuda.key').$data['type'].$data['timestamp'].json_encode($data['data'],
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
            'type' => 'required | in:CREATE,PROFILE_FILLED,ORDER,GIFT',
            'timestamp' => 'required | integer',
            'data' => 'array',
            'sign' => 'required | string',
        ];
//        $message = [
//            'type' => '类型错误',
//            'timestamp' => '时间错误',
//            'data' => '数据错误',
//            'sign' => '签名错误',
//        ];
        $validator = app('validator')->make($param, $rules);
        if($validator->fails()){
            return json_encode([
                'status'=>'FAIL',
                'error_code'=>$validator->errors()->all(),
                'req'=>$param
            ],JSON_UNESCAPED_UNICODE);
        }

        $sign = sha1(strtolower(config('yuda.key').$param['type'].$param['timestamp'].json_encode($param['data'],
                JSON_UNESCAPED_UNICODE)));
        if($sign == $param['sign']){
            switch ($param['type'])
            {
                case 'ORDER':
                    $orderService = new OrderService();
                    $res = $orderService->yudaOrderSyncToThis($param);
                    if($res === true){
                        return json_encode([
                            'status'=>'SUCCEED',
                            'req'=>$param
                        ],JSON_UNESCAPED_UNICODE);
                    }else{
                        return json_encode([
                            'status'=>'FAIL',
                            'error_code'=>$res,
                            'req'=>$param
                        ],JSON_UNESCAPED_UNICODE);
                    }
                    break;
                case 'GIFT':
                    $jobService = new JobService();
                    $res = $jobService->yudaGiftSyncToThis($param);
                    if($res === true){
                        return json_encode([
                            'status'=>'SUCCEED',
                            'req'=>$param
                        ],JSON_UNESCAPED_UNICODE);
                    }else{
                        return json_encode([
                            'status'=>'FAIL',
                            'error_code'=>$res,
                            'req'=>$param
                        ],JSON_UNESCAPED_UNICODE);
                    }
                    break;
                default:
                    return json_encode([
                        'status'=>'FAIL',
                        'error_code'=>'类型错误',
                        'req'=>$param
                    ],JSON_UNESCAPED_UNICODE);
            }
        }else{
            return json_encode([
                'status'=>'FAIL',
                'error_code'=>'签名错误',
                'req'=>$param
            ],JSON_UNESCAPED_UNICODE);
        }
    }

//    密文测试
    public function test()
    {
        $text = '234222';
        $a = passwordEncrypt($text);
//        $b = $this->passwordDecrypt($a);
        echo '234222的密文:'.$a;
        echo '<br>';
        $text = 'qwe123';
        $a = passwordEncrypt($text);
//        $b = $this->passwordDecrypt($a);
        echo 'qwe123的密文:'.$a;
        echo '<br>';
        $text = 'JKL_JkjlsLNL';
        $a = passwordEncrypt($text);
//        $b = $this->passwordDecrypt($a);
        echo 'JKL_JkjlsLNL的密文:'.$a;
        echo '<br>';
//        echo $b;
//        $f = base64_encode($f);
//        $a = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,'B78jVbW5AK4n0fMTWDAh0QpUFec5xPOi',$f,MCRYPT_MODE_CBC,
//            '00000000000000000000000000000000'));
//        echo 'PHP加密后的数据:'.$a;
//        echo '<br />';
//
//        $c = base64_decode(mcrypt_decrypt(MCRYPT_RIJNDAEL_256,'B78jVbW5AK4n0fMTWDAh0QpUFec5xPOi',
//            base64_decode($a),MCRYPT_MODE_CBC,
//            '00000000000000000000000000000000'));
//        echo 'PHP加密在解密后的数据:'.$c;
//        echo strlen($c);
//        echo '<br />';
//        var_dump($c);
////        dd($c);
//        echo '<br />';
//        $b = mcrypt_decrypt(MCRYPT_RIJNDAEL_256,'B78jVbW5AK4n0fMTWDAh0QpUFec5xPOi',
//            base64_decode('ea2o4YFBrVnI8uzc6NK7EpctfWixxwDlAJIlSAkBi1M='),MCRYPT_MODE_CBC,
//            '00000000000000000000000000000000');
//        echo '好好用系统加密后的数据:'.'ea2o4YFBrVnI8uzc6NK7EpctfWixxwDlAJIlSAkBi1M=';
//        echo '<br />';
//        echo '好好用系统加密在解密后的数据:'.$b;
//        echo '<br />';
//        $card= Card::find(2);
//        $b = str_replace('\x14','',$b);
//        echo strlen($b);
//        $card->name=$b;
//        $card->save();
//
//        dd($b);
//        die;
//        $a = Crypt::encrypt('JKL_JkjlsLNL');
//        dd(base64_decode($a));
//
//        try {
//            $decrypted = Crypt::decrypt($a);
//            dd($decrypted);
//        } catch (DecryptException $e) {
//            //
//        }
    }

    public function posttest()
    {
        $data = [
            'type'=>'CREATE',
            'timestamp'=>time(),
            'data'=>[
                'code'=>'123456',
                'mobile'=>'13939992839',
                'nickname'=>'李广义',
                'sex'=>1,
                'birthday'=>'1382694957',
                'password'=>'ABCDEFGHIJKLMN'
            ]
        ];
        $data = [
            'type'=>'GIFT',
            'timestamp'=>time(),
            'data'=>[
                'id'=>'456',
                'summary'=>'营销赠送测试',
                'change_amount'=>'10',
                'cards'=>[
                    '32072589','29888998'
                ]
            ]
        ];
        $data = [
            'type'=>'ORDER',
            'timestamp'=>time(),
            'data'=>[
                'id'=>'456',
                'code'=>'32072589',
                'summary'=>'营销赠送测试',
                'total_fee'=>'20',
                'bonus_require'=>'10',
                'bonus_present'=>'0'
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
        $a = mcrypt_decrypt(MCRYPT_RIJNDAEL_256,config('yuda.mcrypt.key'),$str,MCRYPT_MODE_CBC,config('yuda.mcrypt.iv'));
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