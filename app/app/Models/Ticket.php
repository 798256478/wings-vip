<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
	use SoftDeletes;
    
    protected $table = 'tickets';
    
    protected $fillable = [
        'card_id',
        'ticket_template_id',
    ];
    
    protected $dates = [
        'deleted_at',//importent   
        'verified_at',
    ];
    
    public function ticketTemplate ()
    {
        return $this->belongsTo('App\Models\TicketTemplate');   
    }
}