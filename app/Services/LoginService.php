<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Helpers\ResponseHelper;

class LoginService
{
    public function login(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            return ResponseHelper::error('Invalid credentials', null, 401);
        }

        $user = Auth::user();
        $token = $user->createToken('Personal Access Token')->accessToken;

        return ResponseHelper::success('Login successful', ['token' => $token, 'user' => $user]);
    }
}
