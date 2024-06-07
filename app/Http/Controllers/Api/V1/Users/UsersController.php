<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Helpers\ResponseHelper;
use Exception;

class UsersController extends Controller
{
    public function users(): JsonResponse
    {
        try {
            $users = User::get();
            if ($users) {
                return ResponseHelper::success('Users retrieved successfully', $users, 200);
            } else {
                return ResponseHelper::error('No users found', null, 404);
            }
        } catch (Exception $e) {
            return ResponseHelper::error('Error retrieving users: ' . $e->getMessage(), null, 500);
        }
    }
}
