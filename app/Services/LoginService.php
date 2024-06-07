<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginService
{
    public function login(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            return false;
        }
        $user = Auth::user();
        $token = $user->createToken('Personal Access Token')->accessToken;

        return ['token' => $token];
    }
}
