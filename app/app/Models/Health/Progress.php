<?php

namespace App\Models\Health;

use Illuminate\Database\Eloquent\Model;
class Progress extends Model
{
//	use SoftDeletes;

	protected $table = 'progress';

	protected $guarded = [
		'id',
	];


}
