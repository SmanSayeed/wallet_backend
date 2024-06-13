<?php

use App\Models\User;
use App\Services\LoginService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

// Using Pest's testing functions
use function Pest\Laravel\assertDatabaseHas;

// A utility to set up a fresh application state
beforeEach(function () {
    // $this->refreshApplication();
    $this->loginService = app(LoginService::class);
});

// 1. Test for successful login and OTP dispatch
it('logs in successfully and sends OTP', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $credentials = ['email' => $user->email, 'password' => 'password'];

    $response = $this->loginService->loginWithOtp($credentials);

    expect($response['message'])->toBe('OTP sent successfully. Please verify your OTP.');
    assertDatabaseHas('users', [
        'email' => $user->email,
        // These fields should be updated during login
        'otp' => $user->otp,
        'otp_expires_at' => $user->otp_expires_at,
    ]);
});

// 2. Test for failed login with incorrect credentials
it('fails login with invalid credentials', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $credentials = ['email' => $user->email, 'password' => 'wrongpassword'];

    $response = $this->loginService->loginWithOtp($credentials);

    expect($response['message'])->toBe('Invalid credentials');
});

// 3. Test for successful OTP verification
it('successfully verifies OTP', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
        'otp' => '123456',
        'otp_expires_at' => Carbon::now()->addMinutes(5),
    ]);

    $response = $this->loginService->verifyOtp($user->email, '123456');

    expect($response['message'])->toBe('OTP verified and user authenticated successfully.');
    assertDatabaseHas('users', [
        'email' => $user->email,
        'otp' => null, // OTP should be cleared upon successful verification
    ]);
});

// 4. Test for OTP verification failure due to expiry
it('fails OTP verification due to expiry', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
        'otp' => '123456',
        'otp_expires_at' => Carbon::now()->subMinutes(5),  // OTP is expired
    ]);

    $response = $this->loginService->verifyOtp($user->email, '123456');

    expect($response['message'])->toBe('Invalid or expired OTP.');
});
