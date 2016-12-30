<?php

namespace App\Models;

use Jenssegers\Mongodb\Model as Eloquent;

class QuestionnaireAnswer extends Eloquent {

	protected $collection = 'questionnaire_answers';

	protected $connection = 'mongodb';
	protected $guarded ='_id';

}