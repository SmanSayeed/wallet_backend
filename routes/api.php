<?php

use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|re
*/


// Version 1 API routes
Route::prefix('v1')->group(function () {
    Route::get('/', function () {
        return response()->json(['message' => 'Welcome to the Moby API.']);
        });

    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::get('/verify-email/{id}/{hash}', [UserController::class, 'verify'])->name('verification.verify')->middleware(['auth:api', 'signed']);
    Route::post('/login', [UserController::class, 'login'])->middleware(['verified']);

    Route::get('/users', [UserController::class, 'users']);
});
