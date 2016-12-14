<?php

namespace App\Models\Health;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class Barcode extends Model
{
//	use SoftDeletes;

    protected $table = 'barcodes';

//    protected $primaryKey = 'code';

    protected $fillable = [
        'code',
    ];

    public function customer()
    {
        return $this->hasOne('App\Models\Health\Customer','barcode_id','id');
    }
    
    public function experimentDatas()
    {
        return $this->hasMany('App\Models\Health\ExperimentData');
    }
}
