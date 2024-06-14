<?php

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\DataController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
|
All public API routes are defined here.
|
*/
Route::post('/process-data', [DataController::class, 'processData']);
Route::get('/test', [DataController::class, 'processData']);
// Version 1 API routes
Route::get('/', function () {
    return response()->json(['message' => 'Welcome to the Moby API.']);
});
// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Login with OTP
Route::get('/send-otp', [AuthController::class, 'sendOtp'])->name('send-otp');// it
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');

// Route for unauthorized token
Route::get('/unauthorized', function () {
    return ResponseHelper::error('Unauthorized', null, 401);
})->name('unauthorized');

