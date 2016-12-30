<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{

    protected $table = 'orders';

    protected $fillable = [
        'card_id',
        'number',
        'body',
        'remark',
        'type',
        'channel',
        'total_fee',
        'bonus_require',
        'bonus_present',
        'money_pay_amount',
        'bonus_pay_amount',
        'balance_fee',
        'balance_present',
        'state',
    ];

    protected $dates = [
        'deleted_at',//importent
        'birthday',
    ];

    public function orderDetails()
    {
        return $this->hasMany('App\Models\OrderDetail');
    }

    public function refund(){
        return $this->hasMany('App\Models\Refund')->orderBy('created_at','desc');
    }

    public function orderPayments()
    {
        return $this->hasMany('App\Models\OrderPayment');
    }

    public function card()
    {
        return $this->belongsTo('App\Models\Card');
    }

    public function ticket()
    {
        return $this->belongsTo('App\Models\Ticket');
    }

    public function suit()
    {
        return $this->belongsTo('App\Models\CommoditySpecification');
    }

    public function syncFail()
    {
        return $this->hasOne('App\Models\Yuda\OrderSyncFail');
    }

}
