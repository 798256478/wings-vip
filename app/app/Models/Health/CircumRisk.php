<?php

namespace App\Models\Health;

use Illuminate\Database\Eloquent\Model;

class CircumRisk extends Model
{
    protected $table = 'circum_risks';

    protected $fillable = [
        'tag',
        'project_id',
        'min',
        'max',
        'level',
        'instructions',
    ];

    public function project()
    {
        return $this->belongsTo('App\Models\Health\Project');
    }
}
