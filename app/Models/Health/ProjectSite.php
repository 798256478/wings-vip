<?php

namespace App\Models\Health;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectSite extends Model
{
	use SoftDeletes;

    protected $table = 'project_site';

    protected $fillable = [
        'code',
        'project_id',
        'weight',
        'isPositive',
        'mutation'
    ];

    protected $dates = [
        'deleted_at',
    ];
    protected $casts = [
        'weight' => 'array',
    ];
    public $incrementing = false; 

    public function site()
    {
        return $this->belongsTo('App\Models\Health\Site', 'code', 'code');
    }

   
}
