<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPayment extends Model
{
	
    protected $table = 'order_payments';
    
    protected $fillable = [
        'order_id', 
        'type', 
        'name',
        'amount', 
    ];
    
    protected $touches = ['order'];
    
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }
    
}