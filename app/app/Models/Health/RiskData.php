<?php

namespace App\Models\Health;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiskData extends Model
{
	use SoftDeletes;

    protected $table = 'risk_datas';

    protected $dates = [
        'deleted_at',
    ];
    
    protected $casts = [
        'circum_score' => 'double',
        'total_score' => 'double',
    ];

}
