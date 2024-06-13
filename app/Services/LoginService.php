<?php

namespace App\Services;

use App\Events\SendOtpEmailEvent;
use App\Helpers\ResponseHelper;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Log;

class LoginService
{
    public function loginWithOtp(array $credentials)
    {
        // Validate credentials (email and password)
        if (!Auth::attempt($credentials)) {
            return ResponseHelper::error('Invalid credentials', null, 401);
        }

        // Generate and send OTP
        $user = User::where('email', $credentials['email'])->first();
        $this->sendOtp($user);

        return ResponseHelper::success('OTP sent successfully. Please verify your OTP.', ['email' => $credentials['email']]);
    }

    public function verifyOtp(string $email, string $otp)
    {
        // Find the user by email
        $user = User::where('email', $email)->first();

        // Check if OTP matches and is not expired
        if ($user && $user->otp === $otp && $user->otp_expires_at->isFuture()) {
            // Clear OTP fields after successful verification
            $user->update(['otp' => null, 'otp_expires_at' => null]);

            // Create access token using Passport
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->accessToken;

            // Return success response with token and user details
            return ResponseHelper::success('OTP verified and user authenticated successfully.', ['token' => $token, 'user' => $user]);
        }

        return ResponseHelper::error('Invalid or expired OTP.', null, 400);
    }


     protected function sendOtp(User $user)
    {
        try {
            // Generate OTP
            $otp = rand(100000, 999999);

            // Update user record with OTP and expiry time
            $user->update([
                'otp' => $otp,
                'otp_expires_at' => Carbon::now()->addMinutes(100),
            ]);

            // Dispatch the event to send OTP email
            event(new SendOtpEmailEvent($user, $otp));

            // return ['success' => true];
            return ResponseHelper::success('OTP sent successfully.', null);
        } catch (\Exception $e) {
            Log::error('Failed to send OTP email', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return ResponseHelper::error('Failed to send OTP email', null, 500);
        }
    }
}
