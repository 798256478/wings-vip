<?php

namespace App\Models\Yuda;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderSyncFail extends Model
{
    protected $table = 'order_sync_fail';

    protected $fillable = [
    ];

    protected $dates = [
    ];

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    public function card()
    {
        return $this->belongsTo('App\Models\Card');
    }

}
