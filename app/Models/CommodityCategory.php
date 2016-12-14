<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommodityCategory extends Model
{
	use SoftDeletes;

    protected $table = 'commodity_categories';

    protected $fillable = [
        'name',
        'commission',
    ];

    protected $casts = [
        'commission' => 'double',
    ];

    protected $dates = [
        'deleted_at',//importent
    ];
}
