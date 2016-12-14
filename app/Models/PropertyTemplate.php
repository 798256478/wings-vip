<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyTemplate extends Model
{
	use SoftDeletes;

    protected $table = 'property_templates';

    protected $fillable = [
        'title',
        'color',
        'notice',
        'description',
    ];

    protected $dates = [
        'deleted_at', //importent
    ];

    public function goods()
    {
        return $this->morphMany('App\Models\Good', 'sellable');
    }
}
