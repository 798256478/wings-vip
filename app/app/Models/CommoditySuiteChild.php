<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommoditySuiteChild extends Model
{
    protected $table = 'commodity_suite_children';

    protected $fillable = [
        'suit_id',
        'child_id',
        'count',
    ];


    public function commoditySpecifications()
    {
        return $this->hasOne('App\Models\CommoditySpecification', 'id', 'child_id');
    }
}
