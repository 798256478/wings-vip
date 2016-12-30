<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketTemplate extends Model
{
	use SoftDeletes;

    protected $table = 'ticket_templates';

    protected $fillable = [
        'color',
        'description',
        'get_limit',
        'begin_timestamp',
        'end_timestamp',
    ];

    protected $dates = [
        'deleted_at', //importent
    ];

    public function goods()
    {
        return $this->morphMany('App\Models\Good', 'sellable');
    }

    public function getBeginDisplayTimeAttribute(){
        return convart_timestamp_to_display(strtotime($this->attributes['begin_timestamp']));
    }

    public function getEndDisplayTimeAttribute(){
        return convart_timestamp_to_display(strtotime($this->attributes['end_timestamp']));
    }

    protected $appends = ['begin_display_time', 'end_display_time'];
}
