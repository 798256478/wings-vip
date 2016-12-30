<?php

namespace App\Models;

use Jenssegers\Mongodb\Model as Eloquent;

class Mass extends Eloquent {

    protected $collection = 'mass';

	protected $connection = 'mongodb';

}
