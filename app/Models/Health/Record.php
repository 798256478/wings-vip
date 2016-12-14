<?php

namespace App\Models\Health;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{

	protected $table = 'records';

	protected $guarded = [
		'experiment_data_id',
        'sampleNo',
        'time',
	];
    
    protected $dates = [
        'time',
    ];
    
    public function experimentData()
    {
        return $this->belongsTo('App\Models\Health\ExperimentData');
    }

}
