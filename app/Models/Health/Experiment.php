<?php

namespace App\Models\Health;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Experiment extends Model
{
	use SoftDeletes;

    protected $table = 'experiments';

    protected $fillable = [
        'name',
    ];

    protected $dates = [
        'deleted_at',
    ];

    public function projects()
    {
        return $this->hasMany('App\Models\Health\Project');
    }
}
