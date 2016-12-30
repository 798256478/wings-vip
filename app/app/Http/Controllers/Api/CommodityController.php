<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Http\Request;
use Intervention\Image\Facades\Image;
use App\Services\AuthService;
use App\Services\CommodityService;

class CommodityController extends Controller
{
    protected $authService;
    protected $commodityService;
    public function __construct(AuthService $authService, CommodityService $commodityService)
    {
        $this->authService = $authService;
        $this->commodityService = $commodityService;
    }

    /**
     * 获取所有商品
     *
     * @method getCommodityList
     *
     * @return array
     */
    public function getCommodityList()
    {
        try {
            $this->authService->singleRoleVerify('admin');

            return $this->commodityService->getCommodityList();
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function getCommoditySpecificationsWithoutSuit()
    {
        try {
            $this->authService->singleRoleVerify('admin');

            return $this->commodityService->getCommoditySpecificationsWithoutSuit();
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function getCommodity($id)
    {
        try {
            $this->authService->singleRoleVerify('admin');

            return $this->commodityService->getCommodity($id);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function getCommodityMarketer($id)
    {
        try {
            $this->authService->singleRoleVerify('admin');

            return $this->commodityService->getCommodityMarketer($id);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function addCommodity(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $commodity = $this->checkPostData($request);

            return $this->commodityService->addCommodity($commodity);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function updateCommodity(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $commodity = $this->checkPostData($request);
            if ($request->has('id')) {
                $commodityId = $request->input('id');
                $oldCommodity = $this->commodityService->getCommodity($commodityId);
                if (isset($oldCommodity['name'])) {
                    $tmpCommodity = [];
                    foreach ($commodity as $key => $value) {
                        if ($oldCommodity[$key] == $value) {
                            $tmpCommodity[$key] = $value;
                        }
                    }
                    foreach ($tmpCommodity as $key => $value) {
                        if ($key != 'is_suite' && $key != 'is_need_delivery') {
                            unset($commodity[$key]);
                        }
                    }
                    if (count($commodity) > 0) {
                        return $this->commodityService->updateCommodity($commodity, $commodityId);
                    } else {
                        throw new \Dingo\Api\Exception\StoreResourceFailedException('没有修改任何内容');
                    }
                } else {
                    throw new \Dingo\Api\Exception\StoreResourceFailedException('商品验证失败', ['id' => ['获取失败']]);
                }
            } else {
                throw new \Dingo\Api\Exception\StoreResourceFailedException('商品验证失败', ['id' => ['没有编号']]);
            }
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function deleteCommodity($id)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $oldCommodity = $this->commodityService->getCommodity($id);
            if (isset($oldCommodity['name'])) {
                $this->commodityService->deleteCommodity($id);
            } else {
                throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('没有这个商品');
            }
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    //根据条件查找符合条件的商品，并且返回复杂属性
    public function getCommoditiesByConditions($conditions)
    {
        try {
            return $this->commodityService->getCommoditiesByConditions($conditions);
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
            'name' => ['required', 'max:20'],
            'summary' => ['max:50'],
            'is_suite' => ['required', 'boolean'],
            'is_need_delivery' => ['boolean'],
            'disable_coupon' => ['required', 'boolean'],
            'suit' => ['required_if:is_suite,true', 'array'],
            'image' => ['required', 'array'],
            'is_on_offer' => ['required', 'boolean'],
            'detail' => ['required'],
            'quota_number' => ['required', 'integer', 'min:0'],
            'commission' => ['numeric', 'min:0'],
            'commodity_category_id' => ['required', 'integer'],
        ];
        if ($request->has('id')) {
            $rules['code'] = ['required', 'unique:commodities,code,'.$request->input('id')];
        } else {
            $rules['code'] = ['required', 'unique:commodities,code'];
        }

        if ($request->has('specifications')) {
            if (is_array($request->input('specifications'))) {
                for ($i = 0; $i < count($request->input('specifications')); ++$i) {
                    $rules["specifications.$i.name"] = ['string', 'max:15'];
                    $rules["specifications.$i.price"] = ['required', 'numeric', 'min:0'];
                    $rules["specifications.$i.bonus_require"] = ['required', 'integer', 'min:0'];
                    $rules["specifications.$i.sellable_type"] =
                        ['sometimes', 'in:App\Models\TicketTemplate,App\Models\PropertyTemplate'];
                    $rules["specifications.$i.sellable_quantity"] = ['integer', 'min:0'];
                    $rules["specifications.$i.sellable_validity_days"] = ['integer', 'min:0'];
                    $rules["specifications.$i.stock_quantity"] = ['required', 'integer', 'min:0'];
                    if (isset($request->input('specifications')[$i]['sellable_type'])) {
                        if ($request->input('specifications')[$i]['sellable_type'] == "App\Models\TicketTemplate") {
                            $rules["specifications.$i.sellable_id"] = ['exists:ticket_templates,id'];
                        } elseif ($request->input('specifications')[$i]['sellable_type'] == "App\Models\PropertyTemplate") {
                            $rules["specifications.$i.sellable_id"] = ['exists:property_templates,id'];
                        }
                    }
                }
            } else {
                throw new \Dingo\Api\Exception\StoreResourceFailedException('商品验证失败', ['specifications' => ['必须是数组']]);
            }
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('商品验证失败', ['specifications' => ['不能为空']]);
        }

        $checkArr = ['name', 'summary', 'is_suite', 'suit', 'code', 'is_on_offer',
            'detail', 'image', 'quota_number', 'specifications', 'is_need_delivery',
            'disable_coupon', 'commission', 'commodity_category_id',
        ];

        $commodity = $request->only($checkArr);
        $validator = app('validator')->make($commodity, $rules);
        if (strlen(json_encode($commodity['suit'])) > 1024) {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('商品验证失败', ['suit' => ['长度过长']]);
        }
        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('商品验证失败', $validator->errors());
        }

        return $commodity;
    }

    /**
     * 上传图片并返回url.
     *
     * @method getImage
     *
     * @param object $request 系统定义
     *
     * @return string
     */
    public function getImage(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            if ($request->hasFile('image')) {
                $path = 'upload/'.user_domain().'/';
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $imgName = str_replace('-', '', substr(com_create_guid(), 1, 36)).'.jpg';
                if($request->has('size')){
                    $size = $request->input('size');
                    if ($request->has('type')) {
                        $type = $request->input('type');
                        switch ($type) {
                            case 'logo':
                                $path = $path . 'logo/';
                                $imgName = 'logo.png';
                                if (file_exists($path.$imgName)) {
                                    unlink($path.$imgName);
                                }else {
                                    mkdir($path, 0777, true);
                                }
                                Image::make($request->file('image'))->resize($size, $size)->save($path.$imgName);
                                break;
                            case 'card':
                                $path = $path . 'card/';
                                if (!file_exists($path)) {
                                    mkdir($path, 0777, true);
                                }
                                Image::make($request->file('image'))->resize(315, 160)->save($path.$imgName);
                                break;
                            default:
                                # code...
                                break;
                        }
                    }else {
                        $path = $path . 'icons/';
                        if (!file_exists($path)) {
                            mkdir($path, 0777, true);
                        }
                        Image::make($request->file('image'))->resize($size, $size)->save($path.$imgName);
                    }
                }else{
                    Image::make($request->file('image'))->resize(null, 500, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($path.$imgName);
                }

                return '/'.$path.$imgName;
            } else {
                return '';
            }
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function delImage(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            if ($request->has('image')) {
                $image = $request->input('image');
                if (file_exists(substr($image, 1))) {
                    unlink(substr($image, 1));
                }
            } else {
                return '';
            }
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function getCommoditiesListByPage(Request $request)
    {
        $data=$request->all();
        return $this->commodityService->getCommoditiesListByPage($data);
    }

    public function getCommoditiesByArray(Request $request)
    {
        $items=$request->all();
        $data['commodity']=$this->commodityService->getCommoditiesByArray($items);
        return $data;
    }

    public function getCommodityListWithCategory($categoryId)
    {
        try {
            $this->authService->singleRoleVerify('admin');

            return $this->commodityService->getCommodityListWithCategory($categoryId);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function getCommodityCategory($id)
    {
        try {
            $this->authService->singleRoleVerify('admin');

            return $this->commodityService->getCommodityCategory($id);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function getCommodityCategoryList()
    {
        try {
            $this->authService->singleRoleVerify('admin');

            return $this->commodityService->getCommodityCategoryList();
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function addCommodityCategory(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $rules = [
                'name' => ['required', 'max:20'],
                'commission' => ['numeric', 'min:0'],
            ];
            $commodityCategory = $request->only(['name', 'commission']);
            $validator = app('validator')->make($commodityCategory, $rules);
            if ($validator->fails()) {
                throw new \Dingo\Api\Exception\StoreResourceFailedException('验证失败', $validator->errors());
            }

            return $this->commodityService->addCommodityCategory($commodityCategory);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function editCommodityCategory(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            if ($request->has('id')) {
                $rules = [
                    'id' => ['integer'],
                    'name' => ['required', 'max:20'],
                    'commission' => ['numeric', 'min:0'],
                ];
                $commodityCategory = $request->only(['id', 'name', 'commission']);
                $validator = app('validator')->make($commodityCategory, $rules);
                if ($validator->fails()) {
                    throw new \Dingo\Api\Exception\StoreResourceFailedException('验证失败', $validator->errors());
                }

                return $this->commodityService->editCommodityCategory($commodityCategory);
            }
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function deleteCommodityCategory($id)
    {
        try {
            $this->authService->singleRoleVerify('admin');

            return $this->commodityService->deleteCommodityCategory($commodity);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }
}
