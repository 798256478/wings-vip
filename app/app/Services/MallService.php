<?php

namespace App\Services;

use DB;
use App\Models\Commodity;
use App\Models\Category;
use App\Models\Shop;
use App\Models\Mall;
use App\Exceptions\WingException;


class MallService
{
    public function getMallConfig() {

        $config = Mall::get()->toArray();

        if(isset($config[0])){
            $commodities = $config[0]['commodities'];
            foreach ($commodities['items'] as $key => $value) {
                $commodities['items'][$key] = Commodity::find($value);
            }
            $config[0]['commodities'] = $commodities;
            $shop = $config[0]['shop'];
            $shop['items'] = $this->getAllShop();
            $config[0]['shop'] = $shop;
        }
        
        return $config;
    }

    public function getShop($id = null){

        $shop = null;

        if (is_null($id)) {
            $shop = Shop::orderBy('sort','desc')->first();
        }
        else {
            $shop = Shop::find($id);
        }

        if (is_null($shop))
            return null;

        //获取店铺所有商品ID
        $items =[];
//        $recommended = [];
//        foreach($shop->recommended_items as $key=>$val){
//            if(is_int($val)){
//                $recommended[] = $val;
//            }
//        }
//        $items = $recommended;

        foreach ($shop->categories as $category) {
            $items = array_unique(array_merge($items, $category['items']));
        }       
        
        //查询获得商品信息列表，并转入数组
        $commodities = commodity::with(['specifications' => function($query){
            $query->orderBy('price', 'asc');
        }])->whereIn('id', $items)->get();

        $items = [];
        foreach ($commodities as $item) {
            $items[$item->id] = $item;
        }

        //将店铺商品ID数组转换为商品信息数组
//        if (!empty($shop->recommended_items)) {
//            $list = [];
//            foreach ($shop->recommended_items as $i) {
//                if(is_int($i)) {
//                    if (array_key_exists($i, $items)) {
//                        $list[] = $items[$i];
//                    }
//                }else{
//                    $i['ad'] = true;
//                    $list[] = $i;
//                }
//            }
//            $shop->recommended_items = $list;
//        }

        $categories = $shop->categories;
        foreach ($categories as &$category) {
            $list = [];
            foreach ($category['items'] as $id) {
                if(array_key_exists($id, $items)) {
                    $list[] = $items[$id];
                }
            }
            $category['items'] = $list;
            
        }
        $shop->categories = $categories;

        return $shop;
    }

     public function getAllShop()
    {
        return Shop::orderBy('sort','asc')->get();
    }

    public function saveShopTitle($data)
    {
        if(!isset($data['shopTitle']['title'])||strlen($data['shopTitle']['title'])==0){
            throw new WingException('店铺名不能为空', 401);
        }
        if($data['shopId']==='add') {
            $shop = new Shop();
            $shop->categories=[];
            $isIndex=true;//需要判断保存的店铺在新列表中的位置
        }else{
            $shop=Shop::where('_id',$data['shopId'])->first();
            if($shop->sort===(int)$data['shopTitle']['sort']){
                $isIndex=false;
            }else{
                $isIndex=true;
            }
        }

        //删除不用的图片
        if(isset($shop->recommended_items) && isset($data['shopTitle']['recommended_items'])) {
            foreach ($shop->recommended_items as $image) {
                if (is_array($image)) {
                    $a = true;
                    foreach ($data['shopTitle']['recommended_items'] as $val) {
                        if (is_array($val)) {
                            if ($image['image'] == $val['image']) {
                                $a = false;
                                break;
                            }
                        }
                    }
                    if ($a) {
                        $this->deleteImage($image['image']);
                    }
                }
            }
        }

        $shop->title = $data['shopTitle']['title'];
        $shop->icon = $data['shopTitle']['icon'] or '';
        $shop->image_icon = $data['shopTitle']['image_icon'] or '';
        $shop->recommended_items = $data['shopTitle']['recommended_items'] or [];
        $shop->sort = $data['shopTitle']['sort'] or 0;
        $shop->save();
        if($isIndex){
            $data=$this->getShopIndex($shop->_id);
            $data['isIndex']=$isIndex;
        }else{
            $data['isIndex']=$isIndex;
        }
        return $data;

    }

//    排序值越小,位置越靠前
//    分类数据保存时依据排序值重新排序在保存
    public function saveShopCategory($data)
    {
        $shop=Shop::where('_id',$data['shopId'])->first();
        if(!$shop){
            throw new WingException('店铺不存在', 401);
        }
        $a=$shop->categories;
        if($data['categoryId']==='add'){
            foreach($a as $val){
                if($val['title']==$data['category']['title']){
                    throw new WingException('分类名不能重复', 401);
                }
            }
            $isIndex=true;//需要判断顺序,追加后判断顺序
            $a[]=$data['category'];
        }else{
            if($a[$data['categoryId']]['sort']===$data['category']['sort']){//不需要判断顺序,直接覆盖
                $isIndex=false;
                $a[$data['categoryId']]=$data['category'];
            }else{//需要判断顺序,覆盖后判断顺序
                $isIndex=true;
                $a[$data['categoryId']]=$data['category'];
            }
        }
        if($isIndex){
            $data=$this->getCategoryIndex($a,$data['category']['title']);
            $a=$data['category'];
            $data['isIndex']=$isIndex;
        }else{
            $data['isIndex']=$isIndex;
        }
        $shop->categories=$a;
        $shop->save();
        return $data;
    }

    public function deleteShop($id)
    {
        Shop::where('_id',$id)->delete();
    }

    public function deleteCategory($shopId,$categoryId)
    {
        $shop=Shop::where('_id',$shopId)->first();
        if(!$shop){
            throw new WingException('店铺不存在', 401);
        }
        $a=$shop->categories;
        array_splice($a,$categoryId,1);
        $shop->categories=$a;
        $shop->save();
    }

    //返回保存店铺后的所有店铺数据和保存的店铺在列表中的位置
    //$id保存的店铺的ID
    private function getShopIndex($id)
    {
        $data['shop']=$this->getAllShop();
        foreach($data['shop'] as $key=>$val){
            if($val->_id==$id){
                $data['index']=$key;
                return $data;
            }
        }
    }

    //返回排序后的分类和保存后的分类在分类列表的位置
    //$a  原分类数据
    //$title   要保存的分类数据的名字
    private function getCategoryIndex($a,$title)
    {
        foreach($a as $key=>$val){
            $sort[$key]=$val['sort'];
        }
        array_multisort($sort,SORT_ASC,$a);
        $data['category']=$a;
        foreach($a as $key=>$val){
            if($val['title']==$title){
                $data['index']=$key;
                return $data;
            }
        }
        return $data;
    }

    public function saveShopPage($data)
    {
        $shopPage = Mall::first();
        if(!$shopPage){
            $shopPage = new Mall();
        }
        //删除不用的图片
        if(isset($shopPage['carousel']['items']) && $data['carousel']['items']) {
            foreach ($shopPage['carousel']['items'] as $image) {
                $a = true;
                foreach ($data['carousel']['items'] as $val) {
                    if ($image['image'] == $val['image']) {
                        $a = false;
                        break;
                    }
                }
                if ($a) {
                    $this->deleteImage($image['image']);
                }
            }
        }
        if(isset($shopPage['introduce']['url']) && isset($data['introduce']['url']) &&
            $shopPage['introduce']['url'] != $data['introduce']['url']){
            $this->deleteImage($shopPage['introduce']['url']);
        }
        $shopPage->carousel = $data['carousel'];
        $shopPage->shop = $data['shop'];
        $shopPage->commodities = $data['commodities'];
        $shopPage->introduce = $data['introduce'];
        $shopPage->save();
    }

    public function getShopPage()
    {
        return Mall::first();
    }

    private function deleteImage($image)
    {
        if (file_exists(substr($image, 1))) {
            unlink(substr($image, 1));
        }
    }
}
