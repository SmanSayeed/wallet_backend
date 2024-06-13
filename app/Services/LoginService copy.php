<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginService
{
    public function login(array $credentials)
    {
        // Verify OTP and login
        $user = $this->verifyOtpAndLogin($credentials);

        if (!$user) {
            return ResponseHelper::error('Invalid credentials or OTP.', null, 401);
        }

        // Generate a new token for the user
        $token = $user->createToken('Personal Access Token')->accessToken;

        return ResponseHelper::success('Login successful', ['token' => $token, 'user' => $user]);
    }

    protected function verifyOtpAndLogin(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        // Check if OTP matches and is not expired
        if ($user->otp === $credentials['otp'] && $user->otp_expires_at->isFuture()) {
            $user->update(['otp' => null, 'otp_expires_at' => null]);
            return $user;
        }

        return null;
    }
}
