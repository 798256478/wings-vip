<?php

namespace App\Services;

use App\Models\Address;

class AddressService
{
    public function getAddressList($id)
    {
	    return Address::where('card_id', $id)->get();
    }

    public function getAddress($cardId, $addressId)
    {
        return Address::where('card_id', $cardId)->where('id', $addressId)->first();
    }

    public function getDefaultAddressByCardId($id)
    {
        return Address::where('card_id',$id)->where('isdefault',1)->first();
    }

    public function addAddress($data)
    {
        if($data['isdefault']){
            Address::where('card_id', $data['card_id'])->where('isdefault', 1)
                ->update(['isdefault'=>0]);
        }
        return Address::create($data);
    }

    public function editAddress($cardId, $addressId, $data)
    {
        if($data['isdefault']){
            Address::where('card_id', $cardId)->where('isdefault', 1)
                ->update(['isdefault'=>0]);
        }
        Address::where('card_id', $cardId)->where('id', $addressId)
            ->update($data);
    }

    public function deleteAddress($cardId, $addressId)
    {
        Address::where('id',$addressId)->where('card_id',$cardId)->delete();
    }

    public function getDefaultAddressStringByCardId($id)
    {
        $address = $this->getDefaultAddressByCardId($id);
        
        return $this->buildAddressString($address);
    }

    public function getAddressStringByCardIdAndAddressId($cardId, $addressId)
    {
        $address = $this->getAddress($cardId,$addressId);
        return $this->buildAddressString($address);
    }

    private function buildAddressString($address = null) {
        if ($address){
            $addressStr = $address->name . ',' . 
                          $address->tel . ',' . 
                          $address->province . 
                          $address->city .
                          $address->area . 
                          $address->detail;
            return $addressStr;
        }
        else {
            return false;
        }
    }
}