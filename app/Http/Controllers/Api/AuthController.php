<?php 

namespace App\Http\Controllers\Api;

use Dingo\Api\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
// use Dingo\Api\Routing\Helpers;

use App\Services\AuthService; 

class AuthController extends Controller
{
    
    protected $service;
    
    public function __construct(AuthService $authService)
    {
        $this->service = $authService;
    }
    
    public function login(Request $request)
    {
        $credentials = $request->only('login_name', 'password');
        //return $credentials;
        try{
            return $this->service->login($credentials);
        }
        catch (\Exception $e){
            return json_exception_response($e);
        } 
    }
    
    public  function editPassword(Request $request)
    {
       $data = $request->only('login_name', 'password','newpassword');
       try{
            return $this->service->editPassword($data);
        }
        catch (\Exception $e){
            return json_exception_response($e);
        } 
    }
    
    public function getAuthenticatedUser()
    {
        return $this->service->getAuthenticatedUser();
    }
    
    /**
     * @param Request $request
     * @return mixed
     */
    // public function getUsers ()
    // {
	// 	return $this->service->getAllUsers();
    // }
}