<?php 

namespace App\Models;

use Jenssegers\Mongodb\Model as Eloquent;

class EventExceptionRecord extends Eloquent {

    protected $collection = 'event_exception_records';
	
	protected $connection = 'mongodb';
    
}