<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Http\Request;
use App\Services\AuthService;
use App\Services\TicketService;

class TicketTemplateController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService, TicketService $ticketService)
    {
        $this->authService = $authService;
        $this->ticketService = $ticketService;
    }

    public function getTicketTemplateTypeList()
    {
        return [
            'CASH' => '代金券',
            'DISCOUNT' => '折扣券',
            'GIFT' => '礼品券',
            'GROUPON' => '团购券',
            'GENERAL_COUPON' => '优惠券',
        ];
    }

    /**
     * 获取所有优惠券.
     *
     * @method getTicketTemplateList
     *
     * @return array
     */
    public function getTicketTemplateList()
    {
        try {
            $this->authService->singleRoleVerify('admin');

            return $this->ticketService->getTicketTemplateList();
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    /**
     * 获取所有可用的优惠券.
     *
     * @method getTicketTemplateUsableList
     *
     * @return array
     */
    public function getTicketTemplateUsableList()
    {
        try {
            return $this->ticketService->getTicketTemplateUsableList();
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function getTicketTemplate($id)
    {
        try {
            $this->authService->singleRoleVerify('admin');

            return $this->ticketService->getTicketTemplate($id);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function addTicketTemplate(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $ticketTemplate = $this->checkPostData($request);
            $ticketTemplate['quantity'] = 1000000;
            $ticketTemplate['get_limit'] = 1000000;
            $ticketTemplate['date_info_type'] = 'DATE_TYPE_FIX_TIME_RANGE';
            $ticketTemplate['begin_timestamp'] = getTimeFromW3CTime($ticketTemplate['begin_timestamp']);
            $ticketTemplate['end_timestamp'] = getTimeFromW3CTime($ticketTemplate['end_timestamp']);
            $ticketTemplate['location_id_list'] = null;
            $ticketTemplate['wechat_status'] = 'CARD_STATUS_NOT_VERIFY';
            if (strtotime($ticketTemplate['begin_timestamp']) > strtotime($ticketTemplate['end_timestamp'])) {
                throw new \Dingo\Api\Exception\StoreResourceFailedException('优惠券验证失败', ['end_timestamp' => ['结束时间早于开始时间']]);
            }

            return $this->ticketService->addTicketTemplate($ticketTemplate);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function updateTiecktTemplate(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $ticketTemplate = $this->checkPostData($request);
            unset($ticketTemplate['title'], $ticketTemplate['sub_title'],
                $ticketTemplate['cash_least_cost'], $ticketTemplate['cash_reduce_cost'],
                $ticketTemplate['general_coupon_default_detail'], $ticketTemplate['gift_gift'],
                $ticketTemplate['groupon_deal_detail'], $ticketTemplate['notice'], $ticketTemplate['discount_discount']);
            if ($request->has('id')) {
                $ticketTemplateId = $request->input('id');
                $oldTicketTemplate = $this->ticketService->getTicketTemplate($ticketTemplateId);
                if (isset($oldTicketTemplate['title'])) {
                    $ticketTemplate['begin_timestamp'] = getTimeFromW3CTime($ticketTemplate['begin_timestamp']);
                    $ticketTemplate['end_timestamp'] = getTimeFromW3CTime($ticketTemplate['end_timestamp']);
                    if ($ticketTemplate['begin_timestamp'] == $oldTicketTemplate['begin_timestamp']) {
                        unset($ticketTemplate['begin_timestamp']);
                    }
                    if ($ticketTemplate['end_timestamp'] == $oldTicketTemplate['end_timestamp']) {
                        unset($ticketTemplate['end_timestamp']);
                    }
                    if ($ticketTemplate['color'] == $oldTicketTemplate['color']) {
                        unset($ticketTemplate['color']);
                    }
                    if ($ticketTemplate['description'] == $oldTicketTemplate['description']) {
                        unset($ticketTemplate['description']);
                    }
                    if ($ticketTemplate['disable_online'] == $oldTicketTemplate['disable_online']) {
                        unset($ticketTemplate['disable_online']);
                    }
                    if ($ticketTemplate['disable_shop'] == $oldTicketTemplate['disable_shop']) {
                        unset($ticketTemplate['disable_shop']);
                    }
                } else {
                    throw new \Dingo\Api\Exception\StoreResourceFailedException('优惠券验证失败', ['id' => ['获取失败']]);
                }
            } else {
                throw new \Dingo\Api\Exception\StoreResourceFailedException('优惠券验证失败', ['id' => ['没有编号']]);
            }
            if (count($ticketTemplate) > 1) {
                return $this->ticketService->updateTicketTemplate($ticketTemplate, $ticketTemplateId);
            } else {
                throw new \Dingo\Api\Exception\StoreResourceFailedException('没有修改任何内容');
            }

            return $ticketTemplate;
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function deleteTicketTemplate($id)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $oldTicketTemplate = $this->ticketService->getTicketTemplate($id);
            if (isset($oldTicketTemplate['title'])) {
                $goodList = $this->ticketService->ticketTemplateIdIsUsed($id);
                if (count($goodList) <= 0) {
                    $this->ticketService->deleteTicketTemplate($id);
                } else {
                    throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('该优惠券已被商品引用');
                }
            } else {
                throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('没有这张优惠券');
            }
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    /**
     * 检查传入的数据格式并返回验证过的数据.
     *
     * @method checkPostData
     *
     * @param object $request 系统定义
     *
     * @return array
     */
    private function checkPostData($request)
    {
        $rules = [
            'title' => ['required', 'max:9'],
            'sub_title' => ['max:18'],
            'card_type' => ['required', 'in:GENERAL_COUPON,GROUPON,GIFT,DISCOUNT,CASH'],
            'groupon_deal_detail' => ['required_if:card_type,GROUPON', 'max: 200'],
            'cash_least_cost' => ['required_if:card_type,CASH', 'integer', 'min:0'],
            'cash_reduce_cost' => ['required_if:card_type,CASH', 'integer', 'min:1'],
            'discount_discount' => ['required_if:card_type,DISCOUNT', 'integer', 'min:1', 'max:99'],
            'gift_gift' => ['required_if:card_type,GIFT', 'max: 1500'],
            'general_coupon_default_detail' => ['required_if:card_type,GENERAL_COUPON', 'max: 2000'],
            'color' => ['required', 'size:8'],
            'description' => ['required', 'max:2000'],
            'notice' => ['required', 'max:16'],
            'begin_timestamp' => ['required', 'regex:/^\d{4}[-\/]\d{2}[-\/]\d{2}T\d{2}:\d{2}:\d{2}.000Z$/'],
            'end_timestamp' => ['required', 'regex:/^\d{4}[-\/]\d{2}[-\/]\d{2}T\d{2}:\d{2}:\d{2}.000Z$/'],
            'disable_online' => ['required', 'boolean'],
            'disable_shop' => ['required', 'boolean'],
        ];

        $checkArr = ['title', 'sub_title', 'card_type', 'groupon_deal_detail', 'cash_least_cost',
            'cash_reduce_cost', 'discount_discount', 'gift_gift', 'general_coupon_default_detail',
            'color', 'description', 'notice', 'begin_timestamp', 'end_timestamp', 'disable_online',
            'disable_shop',
        ];

        $ticketTemplate = $request->only($checkArr);
        $validator = app('validator')->make($ticketTemplate, $rules);
        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('优惠券验证失败', $validator->errors());
        }

        return $ticketTemplate;
    }
}
