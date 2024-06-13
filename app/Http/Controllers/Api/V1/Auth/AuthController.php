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

    public function register(RegisterUserRequest $request):JsonResponse
    {
        // dd($request);
        $user = $this->registrationService->register($request->validated());
        return ResponseHelper::success('User registered successfully', $user, 201);
    }


    public function login(LoginUserRequest $request): JsonResponse
    {
        $credentials = $request->validated();

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
    // dump($request,$id,$hash);
    // Find the user by ID
    $user = User::findOrFail($id);

    // Check if the hash matches the user's email hash
    if (! hash_equals($hash, sha1($user->email))) {
        return ResponseHelper::error('Invalid verification link.', 403);
    }

    // Check if the token is valid
    $token = $request->query('token');

    if (! $this->isValidToken($token, $user)) {
        return ResponseHelper::error('Invalid or expired token.', 403);
    }

    // Verify the user's email
    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    // Authenticate the user if needed
    // Auth::login($user); // Uncomment if you want to automatically login the user

    // Return success response
    return ResponseHelper::success('Email verified successfully', $user);
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

}
