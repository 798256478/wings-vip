<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
// use Dingo\Api\Routing\Helpers;

use App\Services\AuthService;
use App\Services\MassService;

class MassController extends Controller
{

    protected $authService;

    public function __construct(AuthService $authService, MassService $massService)
    {
        $this->authService = $authService;
        $this->massService = $massService;
    }

    /**
     * 根据条件查询用户列表
     * @method getQueryResult
     * @param  array        $request 默认的查询数组
     * @return array[部分用户和总计]
     */
    public function getQueryResult(Request $request){
        try{
            $this->authService->singleRoleVerify('admin');
            return $this->massService->getQueryResult($request->all());
        }
        catch (\Exception $e){
            return json_exception_response($e);
        }
    }

    public function getSendTop(){
        try {
            $this->authService->singleRoleVerify('admin');
            return $this->massService->getSendTop();
        } catch (\Exception $e) {
            return json_exception_response($e);
        }

    }

    public function getMassTemplateList(){
        try{
            $this->authService->singleRoleVerify('admin');
            return $this->massService->getMassTemplateList();
        }
        catch (\Exception $e){
            return json_exception_response($e);
        }
    }

    public function getMassTemplate($id){
        try{
            $this->authService->singleRoleVerify('admin');
            return $this->massService->getMassTemplate($id);
        }
        catch (\Exception $e){
            return json_exception_response($e);
        }
    }

    public function saveMassTemplate(Request $request){
        try{
            $this->authService->singleRoleVerify('admin');
            $massTemplate = $request->only(['name', 'template']);
            return $this->massService->saveMassTemplate($massTemplate);
        }
        catch (\Exception $e){
            return json_exception_response($e);
        }
    }

    public function delMassTemplate($id){
        try{
            $this->authService->singleRoleVerify('admin');
            return $this->massService->delMassTemplate($id);
        }
        catch (\Exception $e){
            return json_exception_response($e);
        }
    }

    public function send(Request $request){
        try{
            $this->authService->singleRoleVerify('admin');
            $getArr = ['notice', 'jobs', 'query'];
            $sendMsg = $request->only($getArr);
            $rules = [
                'notice' => ['array'],
                'notice.APP.status' => ['required', 'boolean'],
                'notice.APP.content' => ['required_if:notice.APP.status,true', 'max:40'],
                'notice.APP.image' => ['max:60'],
                'notice.APP.expires_date' => ['required_if:notice.APP.status,true', 'regex:/^\d{4}[-\/]\d{2}[-\/]\d{2}T\d{2}:\d{2}:\d{2}.000Z$/'],
                'notice.SMS.status' => ['required', 'boolean'],
                'notice.SMS.content1' => ['required_if:notice.SMS.status,true', 'max:20'],
                'notice.SMS.content2' => ['required_if:notice.SMS.status,true', 'max:20'],
                'notice.WECHAT.status' => ['required', 'boolean'],
                'notice.WECHAT.content' => ['required_if:notice.WECHAT.status,true', 'max:50'],
                'notice.WECHAT.sendtype' => ['required','in:文本消息,图文消息'],
                'jobs' => ['required', 'array'],
                'query' => ['required', 'array'],
            ];
            $validator = app('validator')->make($sendMsg, $rules);
            if($validator->fails()){
                throw new \Dingo\Api\Exception\StoreResourceFailedException("格式验证失败", $validator->errors());
            }
            //notice统一结构
            $notice = [];
            if ($sendMsg['notice']['APP']['status']) {
                $notice['APP'] = [];
                $expiresDate = getTimeFromW3CTime($sendMsg['notice']['APP']['expires_date']);
                $notice['APP']['expires_date'] = date('Y-m-d H:i:s', strtotime("+1 day", strtotime($expiresDate)) - 1);
                $notice['APP']['content'] = $sendMsg['notice']['APP']['content'];
                if ($sendMsg['notice']['APP']['image'] != '') {
                    $notice['APP']['image'] = $sendMsg['notice']['APP']['image'];
                }else {
                    $notice['APP']['image'] = null;
                }
            }
            if ($sendMsg['notice']['SMS']['status']) {
                $notice['SMS'] = [];
                $notice['SMS']['content1'] = $sendMsg['notice']['SMS']['content1'];
                $notice['SMS']['content2'] = $sendMsg['notice']['SMS']['content2'];
            }
            if ($sendMsg['notice']['WECHAT']['status']) {
                $notice['WECHAT'] = [];
                if($sendMsg['notice']['WECHAT']['sendtype'] == '文本消息'){
                    $notice['WECHAT']['mediaId'] = '';
                    $notice['WECHAT']['content'] = $sendMsg['notice']['WECHAT']['content'];
                }elseif ($sendMsg['notice']['WECHAT']['sendtype'] == '图文消息') {
                    $notice['WECHAT']['mediaId'] = $sendMsg['notice']['WECHAT']['content'];
                    $notice['WECHAT']['content'] = '';
                }else {
                    throw new \Dingo\Api\Exception\StoreResourceFailedException("微信消息类型错误", []);
                }
            }
            $sendMsg['notice'] = $notice;
            $user = $this->authService->getAuthenticatedUser();
            return $this->massService->send($sendMsg, $user);
        }
        catch (\Exception $e){
            return json_exception_response($e);
        }
    }

    public function getMassHistory(){
        try{
            $this->authService->singleRoleVerify('admin');
            return $this->massService->getMassHistory();
        }
        catch (\Exception $e){
            return json_exception_response($e);
        }
    }

    public function getDefaultMass(){
        return [
            ["id"=>"1","isshow"=>false,"text"=>"总消费","minval"=>"","maxval"=>""],
            ["id"=>"4","isshow"=>false,"text"=>"总到店","minval"=>"","maxval"=>""],
            ["id"=>"6","isshow"=>false,"text"=>"会员卡激活","minval"=>"","maxval"=>"","openMin"=>false,"openMax"=>false],
            ["id"=>"5","isshow"=>false,"text"=>"月均到店","minval"=>"","maxval"=>""],
            ["id"=>"2","isshow"=>false,"text"=>"次均消费","minval"=>"","maxval"=>""],
            ["id"=>"3","isshow"=>false,"text"=>"月均消费","minval"=>"","maxval"=>""],
            ["id"=>"7","isshow"=>false,"text"=>"优惠券持有","minval"=>"","maxval"=>""],
            ["id"=>"8","isshow"=>false,"text"=>"次均用券","minval"=>"","maxval"=>""],
            ["id"=>"9","isshow"=>false,"text"=>"最后到店","minval"=>"","maxval"=>"","openMin"=>false,"openMax"=>false],
            ["id"=>"10","isshow"=>false,"text"=>"积分","minval"=>"","maxval"=>""],
            ["id"=>"11","isshow"=>false,"text"=>"余额","minval"=>"","maxval"=>""],
            ["id"=>"12","isshow"=>false,"text"=>"等级","minval"=>"","maxval"=>""],
            ["id"=>"13","isshow"=>false,"text"=>"本月推送","minval"=>"","maxval"=>""],
        ];
    }
}
