<?php

namespace App\Models\Health;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectData extends Model
{
	use SoftDeletes;

    protected $table = 'project_datas';

    protected $dates = [
        'deleted_at',
    ];

}
