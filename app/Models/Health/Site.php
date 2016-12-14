<?php

namespace App\Models\Health;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
	use SoftDeletes;

    protected $table = 'sites';
    protected $primaryKey = 'code';
    protected $fillable = [
        'gene_id',
        'code',
        'mutation',
        'SNPSite',
        'DNASingleType',
    ];

    protected $dates = [
        'deleted_at',
    ];
    
    //  protected $casts = [
    //     'code' => 'string',
    // ];

    public $incrementing = false;

    public function gene()
    {
        return $this->belongsTo('App\Models\Health\Gene');
    }

    public function getDNASingStrAttribute(){
        if($this->attributes['DNASingleType']==0)
            return '正义链';
        else {
            return '反义链';
        }
    }
    
    public function project_sites()
    {
        return $this->hasMany('App\Models\Health\ProjectSite','code');
    }
    
    protected $appends = ['DNASing_Str'];

}
