<?php 

namespace App\Models;

use Jenssegers\Mongodb\Model as Eloquent;

class Setting extends Eloquent {

    protected $collection = 'settings';
	
	protected $connection = 'mongodb';

}