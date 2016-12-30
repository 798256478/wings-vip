<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commodity extends Model
{
	use SoftDeletes;

    protected $table = 'commodities';

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
        'commission' => 'double',
    ];

    public function specifications()
    {
        return $this->hasMany('App\Models\CommoditySpecification');
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($commodity) {
             $commodity->specifications()->delete();
        });
    }

}
