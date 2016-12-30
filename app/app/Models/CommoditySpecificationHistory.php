<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommoditySpecificationHistory extends Model
{   
    protected $table = 'commodity_specification_histories';
    
    protected $fillable = [
        'commodity_history_id',
        'name',
        'full_name',
        'price', 
        'bonus_require', 
        'stock_quantity', 
        'sellable_quantity', 
        'sellable_validity_days',
        'sellable_type',
        'sellable_id',
        'status'
    ];
    
    protected $dates = [
        'deleted_at',//importent
    ];
    
    public function sellable()
    {
        return $this->morphTo();
    }

    public function commodityHistory()
    {
        return $this->belongsTo('App\Models\CommodityHistory');
    }

    public function suiteChildHistories()
    {
        return $this->belongsToMany('App\Models\CommoditySpecificationHistory','commodity_suite_child_histories','suite_history_id','child_history_id')
            ->withPivot('count');
    }

    public function commoditySpecification()
    {
        return $this->belongsTo('App\Models\CommoditySpecification');
    }

}