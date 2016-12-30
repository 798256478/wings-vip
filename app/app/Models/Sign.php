<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sign extends Model
{
	use SoftDeletes;
    
    protected $table = 'signs';
    
    protected $fillable = [
        'card_id',
        'bonus',
        'days'
    ];
    
    protected $dates = [
        'deleted_at',//importent
    ];
}