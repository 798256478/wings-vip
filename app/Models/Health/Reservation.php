<?php

namespace App\Models\Health;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{

    protected $table = 'reservations';

    protected $guarded = [
        'id'
    ];

    public function experimentData()
    {
        return $this->belongsTo('App\Models\Health\ExperimentData');
    }


}
