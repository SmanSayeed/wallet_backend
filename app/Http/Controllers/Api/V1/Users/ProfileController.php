<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserProfileService;

class ProfileController extends Controller
{
    protected $userProfileService;

    public function __construct(UserProfileService $userProfileService)
    {
        $this->userProfileService = $userProfileService;
    }
    public function show(Request $request)
    {
         // Check if the user is authenticated
        if (!$request->user()) {
            // If not authenticated, return unauthorized error response
            return ResponseHelper::error('Unauthorized', null, 401);
        }


        $profileData = $this->userProfileService->getUserProfile($request->user());

        if (!$profileData) {
            // If user profile not found, return error response
            return ResponseHelper::error('User profile not found', null, 404);
        }

        // Return success response with user profile data
        return ResponseHelper::success('User profile retrieved successfully', $profileData);
    }
}
