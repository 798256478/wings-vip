<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Http\Request;
use App\Services\AuthService;
use App\Services\PropertyService;

class PropertyTemplateController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService, PropertyService $propertyService)
    {
        $this->authService = $authService;
        $this->propertyService = $propertyService;
    }

    /**
     * 获取所有服务
     *
     * @method getPropertyTemplateList
     *
     * @return array
     */
    public function getPropertyTemplateList()
    {
        try {
            $this->authService->singleRoleVerify('admin');

            return $this->propertyService->getPropertyTemplateList();
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function getPropertyTemplate($id)
    {
        try {
            $this->authService->singleRoleVerify('admin');

            return $this->propertyService->getPropertyTemplate($id);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function addPropertyTemplate(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $propertyTemplate = $this->checkPostData($request);

            return $this->propertyService->addPropertyTemplate($propertyTemplate);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function updatePropertyTemplate(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $propertyTemplate = $this->checkPostData($request);
            if ($request->has('id')) {
                $propertyTemplateId = $request->input('id');
                $oldPropertyTemplate = $this->propertyService->getPropertyTemplate($propertyTemplateId);
                if (isset($oldPropertyTemplate['title'])) {
                    if ($propertyTemplate['title'] == $oldPropertyTemplate['title']) {
                        unset($propertyTemplate['title']);
                    }
                    if ($propertyTemplate['notice'] == $oldPropertyTemplate['notice']) {
                        unset($propertyTemplate['notice']);
                    }
                    if ($propertyTemplate['color'] == $oldPropertyTemplate['color']) {
                        unset($propertyTemplate['color']);
                    }
                    if ($propertyTemplate['description'] == $oldPropertyTemplate['description']) {
                        unset($propertyTemplate['description']);
                    }
                } else {
                    throw new \Dingo\Api\Exception\StoreResourceFailedException('服务验证失败', ['id' => ['获取失败']]);
                }
            } else {
                throw new \Dingo\Api\Exception\StoreResourceFailedException('服务验证失败', ['id' => ['没有编号']]);
            }
            if (count($propertyTemplate) > 0) {
                return $this->propertyService->updatePropertyTemplate($propertyTemplate, $propertyTemplateId);
            } else {
                throw new \Dingo\Api\Exception\StoreResourceFailedException('没有修改任何内容');
            }

            return $propertyTemplate;
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function deletePropertyTemplate($id)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $oldPropertyTemplate = $this->propertyService->getPropertyTemplate($id);
            if (isset($oldPropertyTemplate['title'])) {
                $goodList = $this->propertyService->propertyTemplateIsUsed($id);
                if (count($goodList) <= 0) {
                    $this->propertyService->deletePropertyTemplate($id);
                } else {
                    throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('该服务已被商品引用无法删除');
                }
            } else {
                throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('没有这个服务');
            }
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function getIcons()
    {
        $list = [
            "fa fa-car", "fa fa-battery-full", "fa fa-beer", "fa fa-bell", "fa fa-birthday-cake",
            "fa fa-book", "fa fa-bullhorn", "fa fa-camera",  "fa fa-coffee", "fa fa-gift",
            "fa fa-comment", "fa fa-credit-card", "fa fa-desktop", "fa fa-diamond",
            "fa fa-glass", "fa fa-graduation-cap", "fa fa-headphones", "fa fa-heart",
            "fa fa-picture-o", "fa fa-male", "fa fa-female", "fa fa-child", "fa fa-bed",
            "fa fa-map", "fa fa-money", "fa fa-music", "fa fa-pencil", "fa fa-paint-brush",
            "fa fa-recycle", "fa fa-users", "fa fa-user", "fa fa-university", "fa fa-trash",
            "fa fa-shopping-cart", "fa fa-shopping-bag", "fa fa-ship", "fa fa-road", "fa fa-jpy",
            "fa fa-weixin", "fa fa-weibo"
        ];
        return $list;
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
            'title' => ['required', 'max:20'],
            'color' => ['required', 'size:8'],
            'icon' => ['string', 'max:20'],
            'image_icon' => ['string', 'max:100'],
            'description' => ['required', 'max:2000'],
            'notice' => ['required', 'max:40'],
        ];

        $checkArr = ['title', 'color', 'icon', 'image_icon', 'description', 'notice'];

        $propertyTemplate = $request->only($checkArr);
        $validator = app('validator')->make($propertyTemplate, $rules);
        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('服务验证失败', $validator->errors());
        }

        return $propertyTemplate;
    }
}
