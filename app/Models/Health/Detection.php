<?php

namespace App\Models\Health;

use Jenssegers\Mongodb\Model as Eloquent;

class Detection extends Eloquent {

	protected $collection = 'detections';

	protected $connection = 'mongodb';

}