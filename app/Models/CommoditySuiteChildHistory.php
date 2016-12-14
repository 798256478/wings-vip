<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommoditySuiteChildHistory extends Model
{
    protected $table = 'commodity_suite_child_histories';

    protected $fillable = [
        'suite_history_id',
        'child_history_id',
        'count',
    ];
}
