<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getAllUsers()
    {
        return User::all();
    }

    public function getUserById($userId)
    {
        return User::where('id', $userId)->first();
    }

    public function addUser($user)
    {
        $User = new User();
        $User->display_name = $user['display_name'];
        $User->roles = $user['roles'];
        $User->login_name = $user['login_name'];
        $User->password = $user['password'];
        $User->save();

        return $User->id;
    }

    public function updateUser($user, $id)
    {
        $User = User::find($id);
        foreach ($user as $key => $value) {
            $User[$key] = $user[$key];
        }
        $User->save();

        return $id;
    }

    public function deleteUser($id)
    {
        User::destroy($id);
    }
}
