<?php

namespace App\Models\Health;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
	use SoftDeletes;

    protected $table = 'projects';

    protected $fillable = [
        'name',
        'sitecount',
        'order',
        'method',
    ];

    protected $dates = [
        'deleted_at',
    ];

    public function projectRisks()
    {
        return $this->hasMany('App\Models\Health\ProjectRisk');
    }
    
    public function CircumRisks()
    {
        return $this->hasMany('App\Models\Health\CircumRisk');
    }
}
