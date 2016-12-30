<?php

namespace App\Models;

use Jenssegers\Mongodb\Model as Eloquent;

class Mall extends Eloquent {

    protected $collection = 'malls';

	protected $connection = 'mongodb';

}
