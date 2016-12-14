<?php

namespace App\Services;

use App\Models\User;
use App\Models\LoginRecord;

class LoginRecordService
{
    public function assembleSql($option,$search)
    {
        $query=new LoginRecord();
        if($option!='ALL'){
            $query=$query->where('roles',$option);
        }
        if(isset($search)){
            $query=$query->where('display_name','like','%'.$search.'%');
        }
        return $query;
    }

    public function getLoginRecords($option,$search,$page)
    {
        $length=14;
        $page=(int)$page;
        if($page>0){
            $query=$this->assembleSql($option,$search);
            return $query->orderBy('created_at', 'desc')->skip($length * ($page - 1))->take($length)->get();
        } else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('页码错误', ['page' => ['页码必须大于0']]);
        }

    }

    public function getTotal($option,$search)
    {
        $query=$this->assembleSql($option,$search);
        return $query->count();
    }

    public function addLoginRecord($user)
    {
        $loginRecords=new LoginRecord();
        $loginRecords->user_id=$user->id;
        $loginRecords->login_name=$user->login_name;
        $loginRecords->display_name=$user->display_name;
        $loginRecords->roles=$user->roles;
        $loginRecords->save();
    }
}
