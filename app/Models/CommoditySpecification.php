<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommoditySpecification extends Model
{
	use SoftDeletes;
    
    protected $table = 'commodity_specifications';
    
    protected $fillable = [
        'commodity_id',
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
    
    public function commodity()
    {
        return $this->belongsTo('App\Models\Commodity');
    }

    public function suiteChildren()
    {
        return $this->belongsToMany('App\Models\CommoditySpecification','commodity_suite_children','suite_id',
            'child_id')
            ->withPivot('count');
    }

    public function CommoditySpecificationHistory()
    {
        return $this->hasMany('App\Models\CommoditySpecificationHistory');
    }

}