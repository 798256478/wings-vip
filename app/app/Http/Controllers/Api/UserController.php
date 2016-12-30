<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Http\Request;
use App\Services\AuthService;
use App\Services\UserService;

class UserController extends Controller
{
    protected $authService;
    protected $userService;

    public function __construct(AuthService $authService, UserService $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    public function getUsers()
    {
        try {
            $this->authService->singleRoleVerify('admin');

            return $this->userService->getAllUsers();
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function getUser($userId)
    {
        try {
            $this->authService->singleRoleVerify('admin');

            return $user = $this->userService->getuserById($userId);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function addUser(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $user = $this->checkPostData($request);

            return $this->userService->addUser($user);
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function updateUser(Request $request)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $user = $this->checkPostData($request);
            if ($request->has('id')) {
                $userId = $request->input('id');
                $oldUser = $this->userService->getUserbyId($userId);
                if (isset($oldUser['display_name'])) {
                    if ($user['display_name'] == $oldUser['display_name']) {
                        unset($user['display_name']);
                    }
                    if ($user['roles'] == $oldUser['roles']) {
                        unset($user['roles']);
                    }
                    if ($user['login_name'] == $oldUser['login_name']) {
                        unset($user['login_name']);
                    }
                    if (!isset($user['password'])) {
                        unset($user['password']);
                    }
                } else {
                    throw new \Dingo\Api\Exception\StoreResourceFailedException('该用户不存在', ['id' => ['获取失败']]);
                }
            } else {
                throw new \Dingo\Api\Exception\StoreResourceFailedException('该用户不存在', ['id' => ['没有编号']]);
            }
            if (count($user) > 0) {
                return $this->userService->updateUser($user, $userId);
            } else {
                throw new \Dingo\Api\Exception\StoreResourceFailedException('没有修改任何内容');
            }

            return $propertyTemplate;
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function deleteUser($id)
    {
        try {
            $this->authService->singleRoleVerify('admin');
            $oldUser = $this->userService->getUserById($id);
            if (isset($oldUser['display_name'])) {
                $loginUser = $this->authService->getAuthenticatedUser();
                if ($id != $loginUser->id) {
                    $this->userService->deleteUser($id);
                } else {
                    throw new \Dingo\Api\Exception\StoreResourceFailedException('不能删除自己');
                }
            } else {
                throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('没有这个用户');
            }
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    private function checkPostData($request)
    {
        $rules = [
            'display_name' => ['required', 'string', 'min:1', 'max:12'],
            'roles' => ['required', 'in:captain,cashier,admin,marketer'],
            'password' => ['required_with:id', 'string', 'min:4', 'max:12'],
            'login_name' => ['required', 'string', 'max:12', 'min:3']
        ];
        if($request->has('id')){
            $rules['login_name'][] = 'unique:users,login_name,'.$request->input('id');
        }else{
            $rules['login_name'][] = 'unique:users,login_name';
        }
        $checkArr = ['display_name', 'roles', 'login_name', 'password'];
        $user = $request->only($checkArr);
        $validator = app('validator')->make($user, $rules);
        if ($validator->fails()) {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('用户信息验证失败', $validator->errors());
        }

        return $user;
    }
}
