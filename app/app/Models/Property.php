<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
	use SoftDeletes;
    
    protected $table = 'properties';
    
    protected $fillable = [
        'card_id',
        'property_template_id',
        'expiry_date',
        'quantity',
    ];
    
    protected $dates = [
        'deleted_at',//importent 
        'expiry_date',       
    ];
    
    public function propertyTemplate ()
    {
        return $this->belongsTo('App\Models\PropertyTemplate');   
    }
    
   
}