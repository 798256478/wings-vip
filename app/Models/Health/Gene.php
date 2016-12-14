<?php

namespace App\Models\Health;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Health\Site;

class Gene extends Model
{
	use SoftDeletes;

    protected $table = 'genes';

    protected $fillable = [
        'name',
        'default_is_positive',
        'default_effect',
    ];

    protected $dates = [
        'deleted_at', 
    ];

    public function sites()
    {
        return $this->hasMany('App\Models\Health\Site');
    }
    
    public function getPositiveStrAttribute(){
        if($this->attributes['default_is_positive']==1)
            return '是';
        else {
            return '否';
        }
    }
    protected $appends = ['positive_str'];


}
