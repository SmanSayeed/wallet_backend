<?php
namespace App\Http\Controllers\Api\V1\Auth;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Services\LoginService;
use App\Services\RegistrationService;
use App\Services\UserService;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginUserRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

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


    public function login(LoginUserRequest $request)
    {
        $credentials = $request->validated();
        return $this->loginService->login($credentials);
    }

    public function logout(Request $request)
    {
        // Revoke the access token
        $request->user()->token()->revoke();

        // Return success response
        return ResponseHelper::success('User logged out successfully');
    }

    public function sendOtp(Request $request)
{
    $user = $request->user();
    $otp = rand(100000, 999999);
    $user->update([
        'otp' => $otp,
        'otp_expires_at' => Carbon::now()->addMinutes(10),
    ]);

    Mail::to($user->email)->send(new OtpMail($otp));
    return response()->json(['message' => 'OTP sent successfully.']);
}

public function verifyOtp(Request $request)
{
    $request->validate([
        'otp' => 'required|numeric',
    ]);

    $user = $request->user();
    if ($user->otp === $request->otp && $user->otp_expires_at->isFuture()) {
        $user->update(['otp' => null, 'otp_expires_at' => null]);
        return response()->json(['message' => 'OTP verified successfully.']);
    }

    return response()->json(['message' => 'Invalid or expired OTP.'], 400);
}

}
