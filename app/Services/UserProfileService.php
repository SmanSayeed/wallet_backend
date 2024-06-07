<?php

namespace App\Services;

use App\Models\User;

class UserProfileService
{
    public function getUserProfile(User $user)
    {
        $fields = ['id', 'name', 'email', 'phone'];
        return collect($user->toArray())->only($fields);
    }
}
