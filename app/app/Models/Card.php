<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Card extends Model
{
	use SoftDeletes;

    protected $table = 'cards';

    protected $fillable = [
        'card_code',
        'openid',
        'nickname',
        'sex',
        'headimgurl',
        'name',
        'birthday',
        'email',
        'level',
        'detail_location',
        'habit',
        'month_receive'
    ];

    protected $dates = [
        'deleted_at',//importent
        'birthday',
    ];

    public function tickets()
    {
        return $this->hasMany('App\Models\Ticket');
    }

    public function properties()
    {
        return $this->hasMany('App\Models\Property');
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }
    
    
    public function getlevelStrAttribute(){
        if(isset($this->attributes['level'])) {
            $level=$this->attributes['level'];
            $settingService=App::make('SettingService');
            $levels=$settingService->get('CARD')['levels'];
            foreach($levels as $model){
                if($model['id']==$level)
                    return $model['name'];
                
            }
        }
        return '';
    }
    
    protected $appends = ['levelStr'];
   
}
