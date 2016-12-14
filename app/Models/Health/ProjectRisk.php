<?php

namespace App\Models\Health;

use Illuminate\Database\Eloquent\Model;

class ProjectRisk extends Model
{
    protected $table = 'project_risks';

    protected $fillable = [
        'project_id',
        'tag',
        'min',
        'max',
        'level',
        'character',
        'instructions',
    ];

    public function project()
    {
        return $this->belongsTo('App\Models\Health\Project');
    }
}
