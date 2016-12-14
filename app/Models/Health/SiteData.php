<?php

namespace App\Models\Health;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Health\Site;

class SiteData extends Model
{
	use SoftDeletes;

    protected $table = 'site_datas';

    protected $fillable = [
        'record_id',
        'code',
        'genotype',
        'singleType',
    ];

    protected $dates = [
        'deleted_at',
    ];
    
    public function getshowGenotypeAttribute(){
        $arr = str_split($this->attributes['genotype']);
        $new_genotype='';
        $site=Site::find($this->attributes['code']);
        foreach ($arr as $key => $value) {
             if(strpos($site->SNPSite, $value) === false){
                if($value=='A' )
                    $new_genotype .= 'T';
                else if( $value=='T' )
                    $new_genotype .= 'A';
                else if( $value=='G' )
                    $new_genotype .= 'C';
                else if( $value=='C' )
                    $new_genotype .= 'G';
             }
             else {
                $new_genotype .= $value;
             }
        }
        return  $new_genotype;
        
    }
    
    public function site()
    {
        return $this->belongsTo('App\Models\Health\Site');
    }
    
    protected $appends = ['showGenotype'];
}
