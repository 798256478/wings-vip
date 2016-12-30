<?php 

namespace App\Models;

use Jenssegers\Mongodb\Model as Eloquent;

class EventRule extends Eloquent {

    protected $collection = 'event_rules';
	
	protected $connection = 'mongodb';
}