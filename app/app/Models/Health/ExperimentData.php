<?php

namespace App\Models\Health;

use Illuminate\Database\Eloquent\Model;
class ExperimentData extends Model
{
//	use SoftDeletes;

	protected $table = 'experiment_datas';

	protected $guarded = [
		'id',
        'barcode_id',
        'experiment_id',
        'progress_id'
	];
    
    public function barcode()
    {
        return $this->belongsTo('App\Models\Health\Barcode');
    }
    
    public function progress()
    {
         return $this->belongsTo('App\Models\Health\Progress');
    }
    
    public function experiment()
    {
         return $this->belongsTo('App\Models\Health\Experiment');
    }


}
