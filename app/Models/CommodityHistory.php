<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommodityHistory extends Model
{
    protected $table = 'commodity_histories';

    protected $fillable = [
        'name',
        'summary',
        'code',
        'image',
        'detail',
        'price',
        'bonus_require',
        'is_single_specification',
        'is_on_offer',
        'disable_coupon',
        'quota_number',
        'commission',
    ];

    protected $dates = [
        'deleted_at',//importent
    ];

    protected $casts = [
        'image' => 'array',
    ];

    public function specificationHistories()
    {
        return $this->hasMany('App\Models\CommoditySpecificationHistory');
    }

}
