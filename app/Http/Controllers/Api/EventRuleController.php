<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
// use Dingo\Api\Routing\Helpers;

use App\Services\AuthService;
use App\Services\EventRuleService;

class EventRuleController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService, EventRuleService $eventRuleService)
    {
        $this->authService = $authService;
        $this->eventRuleService = $eventRuleService;
    }

    /**
     * 获取事件配置列表
     * @method getEventList
     * @return array
     */
    public function getEventList(){
        try {
            $this->authService->singleRoleVerify('admin');
            return $this->eventRuleService->getEventList();
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function getEventRuleList(){
        try {
            $this->authService->singleRoleVerify('admin');
            return $this->eventRuleService->getEventRuleList();
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function getJobList(){
        try {
            $this->authService->singleRoleVerify('admin');
            return $this->eventRuleService->getJobList();
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function getEventRules(){
        try {
            $this->authService->singleRoleVerify('admin');
            return $this->eventRuleService->getEventRules();
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function delEventRule($id){
        try {
            $this->authService->singleRoleVerify('admin');
            return $this->eventRuleService->delRule($id);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function addEventRule(Request $request){
        try {
            $this->authService->singleRoleVerify('admin');
            $eventRule = $this->checkPostData($request);
            $this->eventRuleService->addRule($eventRule);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function updateEventRule(Request $request){
        try {
            $this->authService->singleRoleVerify('admin');
            $eventRule = $this->checkPostData($request);
            $oldEventRule = $this->eventRuleService->getRule($eventRule['_id']);
            if(isset($oldEventRule->title)){
                $this->eventRuleService->updateRule($eventRule);
            }
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    private function checkPostData($request){
        $rules = [
            'title' => ['required', 'string', 'max:20'],
            'event_class' => ['required'],
            'jobs' => ['required', 'array'],
            'conditions' => ['array'],
        ];

        $checkArr = ['title', 'event_class', 'jobs', 'conditions'];
        if($request->has('_id')){
            $checkArr[] = '_id';
            $rules['_id'] = ['required', 'string'];
        }

        $eventRule = $request->only($checkArr);
        $validator = app('validator')->make($eventRule, $rules);
        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('服务验证失败', $validator->errors());
        }

        return $eventRule;
    }
}
