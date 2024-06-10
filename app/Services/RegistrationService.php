<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use App\Events\UserRegistered;

class RegistrationService
{
    public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],
            'address' => $data['address'],
            'country' => $data['country'],
            'post_code' => $data['post_code'],
            'nid' => $data['nid'],
        ]);

        // event(new Registered($user));
        event(new UserRegistered($user));

        return $user;
    }
}
