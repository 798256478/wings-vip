<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{

    protected $table = 'address';
    protected $fillable=['name','tel','card_id','province','city','area','detail','isdefault'];
}
