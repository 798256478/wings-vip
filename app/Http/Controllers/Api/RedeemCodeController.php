<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Http\Request;
use App\Services\AuthService;
use App\Services\RedeemCodeService;

class RedeemCodeController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService, RedeemCodeService $redeemCodeService)
    {
        $this->authService = $authService;
        $this->redeemCodeService = $redeemCodeService;
    }

    public function getRedeemCodeList()
    {
        try {
            $this->authService->singleRoleVerify('admin');

            return $this->redeemCodeService->getRedeemCodeList();
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function getRedeemCode($id)
    {
        try {
            $this->authService->singleRoleVerify('admin');

            $redeemCode = $this->redeemCodeService->getRedeemCode($id);
            if(!$redeemCode->is_many){
                $redeemCode->begin_timestamp = date('Y-m-d H:i:s', $redeemCode->begin_timestamp);
                $redeemCode->end_timestamp = date('Y-m-d H:i:s', $redeemCode->end_timestamp);
            }
            return $redeemCode;
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function addRedeemCode(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $redeemCode = $this->checkPostData($request);
            if(!$redeemCode['is_many']){
                $redeemCode['begin_timestamp'] = strtotime(getTimeFromW3CTime($redeemCode['begin_timestamp']));
                $redeemCode['end_timestamp'] = strtotime(getTimeFromW3CTime($redeemCode['end_timestamp']));
            }

            return $this->redeemCodeService->addRedeemCode($redeemCode);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function updateRedeemCode(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $redeemCode = $this->checkPostData($request);
            if(!$redeemCode['is_many']){
                $redeemCode['begin_timestamp'] = strtotime(getTimeFromW3CTime($redeemCode['begin_timestamp']));
                $redeemCode['end_timestamp'] = strtotime(getTimeFromW3CTime($redeemCode['end_timestamp']));
            }
            if ($request->has('_id')) {
                $redeemCodeId = $request->input('_id');
                $oldRedeemCode = $this->redeemCodeService->getRedeemCode($redeemCodeId);
                if (isset($oldRedeemCode['title'])) {
                    return $this->redeemCodeService->updateRedeemCode($redeemCode, $redeemCodeId);
                } else {
                    throw new \Dingo\Api\Exception\StoreResourceFailedException('兑换码验证失败', ['id' => ['获取失败']]);
                }
            } else {
                throw new \Dingo\Api\Exception\StoreResourceFailedException('兑换码验证失败', ['id' => ['没有编号']]);
            }
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function generationCode($id, $amount)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            return $this->redeemCodeService->generationCode($id, $amount);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function deleteRedeemCode($id)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $oldRedeemCode = $this->redeemCodeService->getRedeemCode($id);
            if (isset($oldRedeemCode['title'])) {
                $this->redeemCodeService->deleteRedeemCode($id);
            } else {
                throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('没有这个兑换码');
            }
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function RedeemCodeHistoryList()
    {
        try {
            $this->authService->singleRoleVerify('admin');
            return $this->redeemCodeService->getHistoryList();
        } catch (\Exception $e) {
            return json_exception_response($e);
        }

    }

    public function RedeemCodeHistory($date, $type)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            return $this->redeemCodeService->getRedeemCodeHistory($date, $type);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    private function checkPostData($request)
    {
        $rules = [
            'title' => ['required', 'max:20'],
            'is_many' => ['required', 'boolean'],
            'codes' => ['required_if:is_many,false', 'array'],
            'jobs' => ['required', 'array'],
            'stock_quantity' => ['required', 'integer'],
        ];
        $checkArr = ['title', 'is_many', 'codes', 'jobs', 'stock_quantity', ];
        if ($request->has('is_many')) {
            if(!$request->input('is_many')){
                $rules['begin_timestamp'] = ['required', 'regex:/^\d{4}[-\/]\d{2}[-\/]\d{2}T\d{2}:\d{2}:\d{2}.000Z$/'];
                $rules['end_timestamp'] = ['required', 'regex:/^\d{4}[-\/]\d{2}[-\/]\d{2}T\d{2}:\d{2}:\d{2}.000Z$/'];
                $checkArr[] = 'begin_timestamp';
                $checkArr[] = 'end_timestamp';
            }
        }


        $redeemCode = $request->only($checkArr);
        $validator = app('validator')->make($redeemCode, $rules);
        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('兑换码验证失败', $validator->errors());
        }

        return $redeemCode;
    }
}
