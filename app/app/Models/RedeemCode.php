<?php 

namespace App\Models;

use Jenssegers\Mongodb\Model as Eloquent;

class RedeemCode extends Eloquent {

    protected $collection = 'redeem_codes';
	
	protected $connection = 'mongodb';

}