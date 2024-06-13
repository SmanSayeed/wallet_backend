<?php

namespace App\Services;

use App\Jobs\SendVerificationEmailJob;
use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use App\Events\UserRegistered;
use Illuminate\Support\Facades\Mail;

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

        // Obtain the OAuth2 token for the user
        // $token = $this->getOAuth2TokenForUser($user);
         // Create a token for the user
         $token = 'EmailVerificationToken';


        // Dispatch the job with the user and token
        event(new UserRegistered($user, $token));

        return $user;
    }

    protected function getOAuth2TokenForUser($user)
    {
        // Create a token for the user
        $tokenResult = $user->createToken('EmailVerificationToken');
        $token = $tokenResult->accessToken;

        return $token;
    }
}
