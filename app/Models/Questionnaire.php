<?php

namespace App\Models;

use Jenssegers\Mongodb\Model as Eloquent;

class Questionnaire extends Eloquent {

	protected $collection = 'questionnaires';

	protected $connection = 'mongodb';

}