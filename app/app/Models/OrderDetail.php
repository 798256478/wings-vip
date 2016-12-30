<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model
{
	
    protected $table = 'order_details';
    
    protected $fillable = [
        'order_id', 
        'commodity_specification_id', 
        'commodity_specification_history_id', 
        'unit_price', 
        'unit_bonus_require', 
		'amount', 
        'total_price', 
        'total_bonus_require', 
    ];
    
    protected $touches = ['order'];
    
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    public function commoditySpecificationHistory()
    {
        return $this->belongsTo('App\Models\CommoditySpecificationHistory');
    }

    public function commoditySpecification()
    {
        return $this->belongsTo('App\Models\CommoditySpecification');
    }
    
    public function refund(){
        return $this->hasMany('App\Models\Refund')->orderBy('created_at','desc');
    }

}