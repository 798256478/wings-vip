<?php

namespace App\Models\Health;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
//	use SoftDeletes;

    protected $table = 'customers';

    protected $guarded = [
        'id'
    ];

    public function barcode()
    {
        return $this->belongsTo('App\Models\Health\Barcode','barcode_id','id');
    }


}
