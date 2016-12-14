<?php

namespace App\Services;

use DB;
use App\Models\Commodity;
use App\Models\CommoditySpecification;
use App\Models\CommoditySuiteChild;
use App\Models\CommodityHistory;
use App\Models\CommoditySpecificationHistory;
use App\Models\CommoditySuiteChildHistory;
use App\Models\CommodityCategory;

class CommodityService
{
    public function getCommodity($id)
    {
        $commodity = Commodity::where('id', $id)->with('specifications')->first();
        if ($commodity->specifications[0]->is_suite) {
            $commoditySuits = CommoditySuiteChild::with('commoditySpecifications.commodity')->where('suite_id', $commodity['specifications'][0]['id'])->get();
            $commodity->suit = $commoditySuits;
        }
        return $commodity;
    }

    public function getCommodityMarketer($id)
    {
        $commodity = Commodity::where('id', $id)->with('specifications')->first();
        if ($commodity->specifications[0]->is_suite) {
            $commoditySuits = CommoditySuiteChild::where('suite_id', $commodity['specifications'][0]['id'])->get();
            $suits = [];
            foreach ($commoditySuits as $suit) {
                $g = CommoditySpecification::where('id', $suit->child_id)->with('commodity')->first();
                $s = [
                    'id' => $suit->child_id,
                    'count' => $suit->count,
                    'full_name' => $g->full_name,
                ];
                $suits[] = $s;
            }
            $commodity->suit = $suits;
        }
        return $commodity;
    }

    public function getCommodityList()
    {
        return Commodity::with(['specifications' => function($query){
            $query->orderBy('price', 'asc');
        }])->get();
    }

    public function getCommoditiesByArray($items)
    {
        return Commodity::whereIn('id',$items)->get();
    }

    public function getCommoditiesListByPage($data)
    {
        $data['page']=(int)$data['page'];
        if($data['page']>0) {
            $length = 10;
            $query = new Commodity();
            if (isset($data['name']) && $data['name']) {
                $query = $query->where('name', 'like', '%' . $data['name'] . '%');
            }
            $data['count'] = $query->count();
            $data['commodity'] = $query->skip($length * ($data['page'] - 1))->take($length)->get();
            return $data;
        }else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('页码错误', ['page' => ['页码必须大于0']]);
        }

    }

    public function getCommoditiesByConditions($conditions)
    {
        $search = Commodity::with('specifications');
        $conditions = json_decode($conditions);
        if (isset($conditions->name) && strlen($conditions->name) > 0) {
            $search->where('name', 'like', '%'.$conditions->name.'%');
        }
        if (isset($conditions->code) && strlen($conditions->code) > 0) {
            $search->where('code', 'like', '%'.$conditions->code.'%');
        }
        if (isset($conditions->status) && strlen($conditions->status) > 0) {
            $search->where('status', $conditions->status);
        }
        $result= $search->orderBy('created_at', 'desc')->get();
        foreach ($result as &$model) {
            if($model->specifications[0]->sellable_id!=null){
                $model->sellable_type=$model->specifications[0]->sellable_type;
            }
            else{
                if($model->specifications[0]->is_suit==false)
                    $model->sellable_type="commodity";
                else
                    $model->sellable_type="suit";
            }
               
        }
        return  $result;
    }

    public function getCommoditySpecificationsWithoutSuit()
    {
        return CommoditySpecification::with('commodity')->where('is_suite', false)->get();
    }

    public function getCommoditySpecificationsHistoryLastByArray($list)
    {
        $data = [];
        foreach($list as $id){
            $data[] = CommoditySpecificationHistory::with('commodityHistory','suiteChildHistories.commodityHistory', 'commoditySpecification')
                ->where('commodity_specification_id',$id)->orderBy('id','desc')->first();
        }
        return $data;
    }

    public function addCommodity($commodity)
    {
        DB::transaction(function () use ($commodity) {
            $Commodity = new Commodity();
            $Commodity->name = $commodity['name'];
            if (isset($commodity['summary'])) {
                $Commodity->summary = $commodity['summary'];
            }else {
                $Commodity->summary = '';
            }
            $Commodity->is_on_offer = $commodity['is_on_offer'];
            $Commodity->code = $commodity['code'];
            $Commodity->image = $commodity['image'];
            $Commodity->detail = $commodity['detail'];
            $Commodity->quota_number = $commodity['quota_number'];
            $Commodity->disable_coupon = $commodity['disable_coupon'];
            $Commodity->commodity_category_id = $commodity['commodity_category_id'];
            $Commodity->commission = $commodity['commission'];
            if (count($commodity['specifications']) == 1) {
                $Commodity->is_single_specification = true;
            }
            $Commodity->save();
            foreach ($commodity['specifications'] as $val) {
                $CommoditySpecification = new CommoditySpecification();
                if (isset($val['name']) && $val['name'] != '') {
                    $CommoditySpecification->name = $val['name'];
                    $CommoditySpecification->full_name = $commodity['name'] . '-' . $val['name'];
                }else {
                    $CommoditySpecification->name = '';
                    $CommoditySpecification->full_name = $commodity['name'];
                }
                $CommoditySpecification->is_suite = $commodity['is_suite'];
                $CommoditySpecification->is_need_delivery = $commodity['is_need_delivery'];
                $CommoditySpecification->price = $val['price'];
                $CommoditySpecification->bonus_require = $val['bonus_require'];
                $CommoditySpecification->stock_quantity = $val['stock_quantity'];
                $CommoditySpecification->is_on_offer = $val['is_on_offer'];
                if (isset($val['sellable_type'])) {
                    $CommoditySpecification->sellable_type = $val['sellable_type'];
                }
                if (isset($val['sellable_id'])) {
                    $CommoditySpecification->sellable_id = $val['sellable_id'];
                }
                if (isset($val['sellable_quantity'])) {
                    $CommoditySpecification->sellable_quantity = $val['sellable_quantity'];
                }
                if (isset($val['sellable_validity_days'])) {
                    $CommoditySpecification->sellable_validity_days = $val['sellable_validity_days'];
                }
                $Commodity->specifications()->save($CommoditySpecification);
            }
            $this->editLowerPrice($Commodity->id);
            if ($Commodity->specifications[0]->is_suite) {
                $commoditySuits = [];
                $newCommodity = Commodity::where('id', $Commodity->id)->with('specifications')->first();
                foreach ($commodity['suit'] as $val) {
                    $suit = [
                        'suite_id' => $newCommodity['specifications'][0]['id'],
                        'child_id' => $val['id'],
                        'count' => $val['count'],
                    ];
                    $commoditySuits[] = $suit;
                }
                CommoditySuiteChild::insert($commoditySuits);
            }
            //复制一份历史
            $this->createHistory($Commodity->id);
            return $Commodity->id;
        });
    }

    public function updateCommodity($commodity, $id)
    {
        DB::transaction(function () use ($commodity, $id) {
            $Commodity = Commodity::find($id);
            foreach ($commodity as $key => $value) {
                if ($key != 'specifications' && $key != 'suit' && $key != 'is_suite' && $key != 'is_need_delivery') {
                    $Commodity[$key] = $commodity[$key];
                }
            }
            if (count($commodity['specifications']) > 1) {
                $Commodity->is_single_specification = false;
            }else {
                $Commodity->is_single_specification = true;
            }
            $Commodity->save();
            $oldCommoditySpecifications = CommoditySpecification::where('commodity_id', $id)->get()->toArray();
            $oldspecificationId = array_column($oldCommoditySpecifications, 'id');
            foreach ($commodity['specifications'] as $val) {
                if (isset($val['id'])) {
                    $CommoditySpecification = CommoditySpecification::find($val['id']);
                    if(($index = array_search($val['id'], $oldspecificationId)) !== false) {
                        unset($oldspecificationId[$index]);
                    }
                    $flag = false;
                    foreach ($val as $key => $value) {
                        if($key == 'full_name'){
                            if (isset($val['name']) && $val['name'] != '') {
                                if ($CommoditySpecification[$key] != $Commodity->name . '-' . $val['name']) {
                                    $CommoditySpecification[$key] = $Commodity->name . '-' . $val['name'];
                                    $flag = true;
                                }
                            }else {
                                $CommoditySpecification[$key] = $Commodity->name;
                                $flag = true;
                            }
                        }elseif ($key == 'is_suite' || $key == 'is_need_delivery') {
                            $CommoditySpecification[$key] = $commodity[$key];
                        }else {
                            if ($CommoditySpecification[$key] != $val[$key]) {
                                $CommoditySpecification[$key] = $val[$key];
                                $flag = true;
                            }
                        }
                    }
                    if ($flag) {
                        $CommoditySpecification->save();
                    }
                } else {
                    $CommoditySpecification = new CommoditySpecification();
                    if (isset($val['name']) && $val['name'] != '') {
                        $CommoditySpecification->name = $val['name'];
                        $CommoditySpecification->full_name = $Commodity['name'] . $val['name'];
                    }else {
                        $CommoditySpecification->full_name = $Commodity['name'];
                    }
                    $CommoditySpecification->is_suite = $commodity['is_suite'];
                    $CommoditySpecification->is_need_delivery = $commodity['is_need_delivery'];
                    $CommoditySpecification->price = $val['price'];
                    $CommoditySpecification->bonus_require = $val['bonus_require'];
                    $CommoditySpecification->stock_quantity = $val['stock_quantity'];
                    if (isset($val['is_on_offer'])) {
                        $CommoditySpecification->is_on_offer = $val['is_on_offer'];
                    } else {
                        $CommoditySpecification->is_on_offer = $Commodity->is_on_offer;
                    }
                    if (isset($val['sellable_type'])) {
                        $CommoditySpecification->sellable_type = $val['sellable_type'];
                    }
                    if (isset($val['sellable_id'])) {
                        $CommoditySpecification->sellable_id = $val['sellable_id'];
                    }
                    if (isset($val['sellable_quantity'])) {
                        $CommoditySpecification->sellable_quantity = $val['sellable_quantity'];
                    }
                    if (isset($val['sellable_validity_days'])) {
                        $CommoditySpecification->sellable_validity_days = $val['sellable_validity_days'];
                    }
                    $Commodity->specifications()->save($CommoditySpecification);
                }
            }
            if (count($oldspecificationId) > 0) {
                foreach ($oldspecificationId as $val) {
                    CommoditySpecification::destroy($val);
                }
            }
            $this->editLowerPrice($Commodity->id);
            if ($Commodity->specifications[0]->is_suite) {
                $commoditySuits = CommoditySuiteChild::where('suite_id', $commodity['specifications'][0]['id'])->get()->toArray();
                $ids = array_column($commoditySuits, 'id');
                CommoditySuiteChild::destroy($ids);
                $commoditySuits = [];
                foreach ($commodity['suit'] as $val) {
                    $suit = [
                        'suite_id' => $Commodity['specifications'][0]['id'],
                        'child_id' => $val['id'],
                        'count' => $val['count'],
                    ];
                    $commoditySuits[] = $suit;
                }
                CommoditySuiteChild::insert($commoditySuits);
            }
            //复制一份历史
            $this->createHistory($Commodity->id);
        });

        return $id;
    }

    /**
     * 新增或修改的同时添加一份历史
     * @method createHistory
     * @param  int        $commodityId [description]
     * @return null
     */
    private function createHistory($commodityId)
    {
        $commodity = Commodity::where('id', $commodityId)->with('specifications')->first();
        $commodityHistory = new CommodityHistory();
        $commodityHistory->commodity_id = $commodity->id;
        $commodityHistory->name = $commodity->name;
        $commodityHistory->summary = $commodity->summary;
        $commodityHistory->code = $commodity->code;
        $commodityHistory->image = $commodity->image;
        $commodityHistory->detail = $commodity->detail;
        $commodityHistory->price = $commodity->price;
        $commodityHistory->bonus_require = $commodity->bonus_require;
        $commodityHistory->is_single_specification = $commodity->is_single_specification;
        $commodityHistory->disable_coupon = $commodity->disable_coupon;
        $commodityHistory->quota_number = $commodity->quota_number;
        $commodityHistory->commodity_category_id = $commodity->commodity_category_id;
        $commodityHistory->commission = $commodity->commission;
        $commodityHistory->save();
        foreach ($commodity->specifications as $specification) {
            $spec = new CommoditySpecificationHistory();
            $spec->commodity_history_id = $commodityHistory->id;
            $spec->commodity_specification_id = $specification->id;
            $spec->name = $specification->name;
            $spec->full_name = $specification->full_name;
            $spec->price = $specification->price;
            $spec->bonus_require = $specification->bonus_require;
            $spec->sellable_type = $specification->sellable_type;
            $spec->sellable_id = $specification->sellable_id;
            $spec->sellable_quantity = $specification->sellable_quantity;
            $spec->sellable_validity_days = $specification->sellable_validity_days;
            $spec->is_suite = $specification->is_suite;
            $spec->is_need_delivery = $specification->is_need_delivery;
            $commodityHistory->specificationHistories()->save($spec);
        }
        if ($commodity->specifications[0]->is_suite) {
            $commodityHistorySuits = [];
            $suiteChildren = CommoditySuiteChild::where('suite_id', $commodity->specifications[0]->id)
                ->with('commoditySpecifications')->get();
            foreach ($suiteChildren as $suite) {
                $specificationHistory = CommoditySpecificationHistory::where('commodity_specification_id', $suite->commoditySpecifications->id)
                    ->orderBy('id', 'desc')->first();
                $suit = [
                    'suite_history_id' => $commodityHistory->specificationHistories[0]->id,
                    'child_history_id' => $specificationHistory->id,
                    'count' => $suite->count,
                ];
                $commodityHistorySuits[] = $suit;
            }
            CommoditySuiteChildHistory::insert($commodityHistorySuits);
        }
        $this->isBelongSuite($commodityId);
    }

    private function isBelongSuite($commodityId)
    {
        $specifications = CommoditySpecification::where('commodity_id', $commodityId)->get();
        foreach ($specifications as $specification) {
            $suitChildArr = CommoditySuiteChild::where('child_id', $specification->id)->get();
            if (count($suitChildArr) > 0) {
                foreach ($suitChildArr as $value) {
                    $spec = CommoditySpecification::where('id', $value->suite_id)->first();
                    $this->createHistory($spec->commodity_id);
                }
            }
        }
    }

    private function editLowerPrice($commodityId)
    {
        $specificationList = CommoditySpecification::where('commodity_id', $commodityId)->orderBy('price', 'asc')->get()->toArray();
        $commodity = Commodity::where('id', $commodityId)->first();
        $commodity->price = $specificationList[0]['price'];
        $commodity->bonus_require = $specificationList[0]['bonus_require'];
        $commodity->save();
    }

    public function deleteCommodity($id)
    {
        $commodity = Commodity::where('id', $id)->with('specifications')->first();
        if ($Commodity->specifications[0]->is_suite) {
            $commoditySuits = CommoditySuiteChild::where('suite_id', $commodity['specifications'][0]['id'])->get()->toArray();
            $ids = array_column($commoditySuits, 'id');
            CommoditySuiteChild::destroy($ids);
        }
        $commodity->delete();
    }

    public function imgIsUsed($imgName)
    {
        $commoditys = Commodity::where('image', 'like', '%'.$imgName.'%')
            ->orWhere('detail', 'like', '%'.$imgName.'%')->get();
        if (count($commoditys) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getSuitCommodityById($id)
    {
        return CommoditySpecification::with('commodity','suitChildren')->where('id',$id)->first();
    }

    public function getCommodityListWithCategory($categoryId)
    {
        return Commodity::where('commodity_category_id', $categoryId)->get();
    }

    public function getCommodityCategory($id)
    {
        return CommodityCategory::where('id', $id)->first();
    }

    public function getCommodityCategoryList()
    {
        return CommodityCategory::get();
    }

    public function addCommodityCategory($category)
    {
        $commodityCategory = new CommodityCategory();
        $commodityCategory->name = $category['name'];
        $commodityCategory->commission = $category['commission'];
        $commodityCategory->save();
        return $commodityCategory->id;
    }

    public function editCommodityCategory($category)
    {
        $commodityCategory = CommodityCategory::where('id', $category['id'])->first();
        if(isset($commodityCategory->id)) {
            $commodityCategory->name = $category['name'];
            $commodityCategory->commission = $category['commission'];
            $commodityCategory->save();
        }
    }

    public function deleteCommodityCategory($id)
    {
        $commodityCategory = CommodityCategory::where('id', $id)->first();
        $commodityCategory->delete();
    }
}
