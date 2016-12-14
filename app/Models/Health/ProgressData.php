<?php

namespace App\Models\Health;

use Illuminate\Database\Eloquent\Model;
class ProgressData extends Model
{
//	use SoftDeletes;

	protected $table = 'progress_datas';

	protected $guarded = [
		'id',
        'experiment_data_id',
        'progress_id'
	];

//	public function project()
//	{
//		return $this->belongsTo('App\Models\Health\Project','projects_id','progress_id');
//	}


//    protected $dates = [
//        'deleted_at', //importent
//    ];

//    public function sites()
//    {
//        return $this->hasMany('App\Models\Health\Site');
//    }

}
