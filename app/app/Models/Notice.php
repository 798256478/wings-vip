<?php

namespace App\Models;

use Jenssegers\Mongodb\Model as Eloquent;

class Notice extends Eloquent {

	protected $collection = 'notices';

	protected $connection = 'mongodb';

}