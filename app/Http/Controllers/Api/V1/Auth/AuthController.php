<?php
namespace App\Http\Controllers\Api\V1\Auth;

use App\Events\SendOtpEmailEvent;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use App\Services\LoginService;
use App\Services\RegistrationService;
use App\Services\UserService;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginUserRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $registrationService;
    protected $loginService;

    public function __construct(RegistrationService $registrationService, LoginService $loginService)
    {
        $this->registrationService = $registrationService;
        $this->loginService = $loginService;
    }

    public function register(RegisterUserRequest $request): JsonResponse
    {
        // dd($request);
        $user = $this->registrationService->register($request->validated());
        return ResponseHelper::success('User registered successfully', $user, 201);
    }


    public function login(LoginUserRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        // Check if user exists and email is verified
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !$user->hasVerifiedEmail()) {
            return ResponseHelper::error('Your email address is not verified.', null, 403);
        }

        // Attempt login with OTP verification
        return $this->loginService->loginWithOtp($credentials);
    }


    public function logout(Request $request)
    {
        // Revoke the access token
        $request->user()->token()->revoke();

        // Return success response
        return ResponseHelper::success('User logged out successfully');
    }


    public function verify(Request $request, $id, $hash)
{
    // Find the user by ID
    $user = User::findOrFail($id);

    // Get frontend base URL from the environment
    $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
    $frontendUrl .= '/auth/login?message=';

    // Check if the hash matches the user's email hash
    if (!hash_equals($hash, sha1($user->email))) {
        return redirect()->to($frontendUrl . 'Invalid+verification+link');
    }

    // Check if the token is valid
    $token = $request->query('token');

    if (!$this->isValidToken($token, $user)) {
        return redirect()->to($frontendUrl . 'Invalid+or+expired+token');
    }

    // Verify the user's email
    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    // Authenticate the user if needed
    // Auth::login($user); // Uncomment if you want to automatically login the user

    // Redirect to frontend login page with success message
    return redirect()->to($frontendUrl . 'Otp+verified+successfully,+please+login');
}



    protected function isValidToken($token, $user)
    {
        return true;

        // Retrieve the user's tokens from oauth_access_tokens table
        // $tokens = $user->tokens;

        // dd($tokens);
        // Check if the provided token matches any of the user's valid tokens
        // foreach ($tokens as $userToken) {
        //     dump(Hash::make($token),$userToken->id);
        //     dd(Hash::check($token, $userToken->id));
        //     // Compare hashed token using Hash::check
        //     if (Hash::check($token, $userToken->id)) {
        //         return true;
        //     }
        // }

        // return false;
    }
    public function verifyOtp(Request $request)
    {
        // Validate request data
        $request->validate([
            'email' => 'required|string|email',
            'otp' => 'required|string',
        ]);

        // Verify OTP
        $response = $this->loginService->verifyOtp($request->email, $request->otp);

        // Return response using ResponseHelper
        return $response;
    }

    public function resendVerificationEmail(Request $request): JsonResponse
    {
        // Validate the request
        $request->validate([
            'email' => 'required|string|email',
        ]);

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ResponseHelper::error('User not found.', null, 404);
        }

        if ($user->hasVerifiedEmail()) {
            return ResponseHelper::error('Email address is already verified.', null, 400);
        }

        // Use the registration service to resend the verification email
        $this->registrationService->sendVerificationEmail($user);

        return ResponseHelper::success('Verification email resent successfully.', null, 200);
    }

}
