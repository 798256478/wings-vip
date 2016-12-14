<?php

namespace App\Models\Health;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectGene extends Model
{
	use SoftDeletes;

    protected $table = 'project_gene';
    protected $primaryKey = array('project_id','gene_id');//laravel bug双主键必须自己写sql
    protected $fillable = [
        'project_id',
        'gene_id',
        'effect',
    ];

    protected $dates = [
        'deleted_at',
    ];
     public $incrementing = false;

    public function gene()
    {
        return $this->belongsTo('App\Models\Health\Gene');
    }
}
