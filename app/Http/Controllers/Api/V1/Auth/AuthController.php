<?php
namespace App\Http\Controllers\Api\V1\Auth;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\LoginService;
use App\Services\RegistrationService;
use App\Services\UserService;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginUserRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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

}
