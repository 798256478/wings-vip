<?php 

namespace App\Models;

use Jenssegers\Mongodb\Model as Eloquent;

class Shop extends Eloquent {

    protected $collection = 'shops';
	
	protected $connection = 'mongodb';

}