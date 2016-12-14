<?php

namespace App\Services;

use App\Models\LoginRecord;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use App\Exceptions\WingException;
use App\Services\LoginRecordService;
use App\Models\User;

class AuthService
{

	protected $auth;
    public $num = 0;

    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    public function login($credentials)
    {
        if (! $token = $this->auth->attempt($credentials)) {
            throw new WingException("invalid_credentials", 401);
        }
        $user = User::where('login_name', $credentials['login_name'])
				->first();
        $user['token'] = $token;

        $loginRecordService = new LoginRecordService;
        $loginRecordService->addLoginRecord($user);
        
        return $user;
    }
    
    public function editPassword($data)
    {
       if (! $token = $this->auth->attempt($data)) {
            throw new WingException("原始密码错误", 401);
        }
       
        $user = User::where('login_name', $data['login_name'])
            ->first();
        $user->setPasswordAttribute($data['newpassword']);
        $user->save();
        $user['token'] = $token;
        return  $user;
    }

    public function getAuthenticatedUser()
    {
        try {

            if (! $user = $this->auth->parseToken()->authenticate()) {
                throw new WingException("user_not_found", 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            throw new WingException("token_expired", $e->getStatusCode(), 0, $e);

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            throw new WingException("token_invalid", $e->getStatusCode(), 0, $e);

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            throw new WingException("token_absent", $e->getStatusCode(), 0, $e);

        }

        return $user;
    }

    public function singleRoleVerify($role)
    {
        $user = $this->getAuthenticatedUser();
        $roles = explode(',', $user->roles);

        if (in_array($role, $roles)){
            return TRUE;
        }else{
            throw new WingException("不具备操作所需角色身份", 401);
        }
    }
    
    public function rolesVerifyWithOr($roles)
    {
        $user = $this->getAuthenticatedUser();
        $userRoles = explode(',', $user->roles);
        $roles = explode(',', $roles);
        $match=false;
        foreach ($roles as $role) {
             if (in_array($role, $roles)){
                  $match=true;
                  break;
             }
        }
        if($match){
            return true;
        }
        else{
            throw new WingException("不具备操作所需角色身份", 401);
        }
    }
    
}
