<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Refund extends Model
{

    protected $table = 'refunds';

    protected $fillable = [
        'id',
        'order_detail_id',
        'money',
        'is_received',
        'type',
        'state',
    ];

    protected $dates = [
        'deleted_at',
    ];

    public function orderDetail()
    {
        return $this->belongsTo('App\Models\OrderDetail');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }
}
