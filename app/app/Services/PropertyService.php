<?php

namespace App\Services;

use App\Models\Property;
use App\Models\PropertyTemplate;
use App\Exceptions\WingException;
use App\Events\UseProperty;
use App\Models\Card;
use App\Models\CommoditySpecification;

class PropertyService
{
    public function getCardPropertyList($cardId){
        return Property::with('PropertyTemplate')
                ->where('card_id', $cardId) ->where('quantity','>', 0)
                ->orderBy('created_at', 'desc')->get();
    }

    public function writeoffProperty($id){
        $property=Property::find($id);
        $property->quantity=$property->quantity-1;
        $property->save();
        $card=Card::find($property->card_id);
        event(new UseProperty($card,$property));
    }

    /*******************property template******************************/
    public function getPropertyTemplateList(){
        return PropertyTemplate::all();
    }

    public function getPropertyTemplate($id){
        return PropertyTemplate::where('id', $id)->first();
    }

    public function addPropertyTemplate($propertyTemplate){
        $PropertyTemplate = new PropertyTemplate;
        $PropertyTemplate->title = $propertyTemplate['title'];
        $PropertyTemplate->color = $propertyTemplate['color'];
        if(isset($propertyTemplate['icon'])){
            $PropertyTemplate->icon = $propertyTemplate['icon'];
        }
        if(isset($propertyTemplate['image_icon'])){
            $PropertyTemplate->image_icon = $propertyTemplate['image_icon'];
        }
        $PropertyTemplate->description = $propertyTemplate['description'];
        $PropertyTemplate->notice = $propertyTemplate['notice'];
        $PropertyTemplate->save();
        return $PropertyTemplate->id;
    }

    /**
     * 更新服务
     * @method updatePropertyTemplate
     * @param  array               $propertyTemplate 将要修改的内容
     * @param  int               $id             要修改的id
     * @return int
     */
    public function updatePropertyTemplate($propertyTemplate, $id){
        $PropertyTemplate = PropertyTemplate::find($id);
        foreach ($propertyTemplate as $key => $value) {
            $PropertyTemplate[$key] = $propertyTemplate[$key];
        }
        $PropertyTemplate->save();
        return $id;
    }

    public function deletePropertyTemplate($id){
        $propertyTemplate = $this->getPropertyTemplate($id);
        if($propertyTemplate->image_icon != ''){
            unlink(substr($propertyTemplate->image_icon, 1));
        }
        PropertyTemplate::destroy($id);
    }

    public function propertyTemplateIsUsed($id){
        return CommoditySpecification::where('sellable_type', 'property_templates')->where('sellable_id', $id)->get();
    }

    public function search($val){
        return PropertyTemplate::where('title', 'like', '%'.$val.'%')->get();
    }


    public function AddProperty($cardId,$templateId,$quantity,$validity_days)
    {
      $propertys=Property::where('card_id',$cardId)->where('property_template_id',$templateId)->get();
      if(count($propertys) == 0){
            $property = new Property;
            $property->card_id = $cardId;
            $property->property_template_id = $templateId;
            $property->expiry_date = date('Y-m-d H:i:s', strtotime($validity_days.' day'));
            $property->quantity = $quantity;
            $property->save();
      }
      else{
          $property=$propertys[0];
          $property->quantity+= $quantity;
          $property->expiry_date = strtotime('+'.$validity_days.' days', strtotime($property->expiry_date));
          $property->save();
      }

    }
}
