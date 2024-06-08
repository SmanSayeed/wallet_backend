<?php

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Users\ProfileController;
use App\Http\Controllers\Api\V1\Users\UsersController;
use App\Http\Controllers\Api\V1\Wallets\CurrencyController;
use App\Http\Controllers\Api\V1\Wallets\TransactionController;
use App\Http\Controllers\Api\V1\Wallets\WalletDenominationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Wallets\WalletController;
use App\Http\Controllers\Api\V1\Wallets\DenominationController;

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

    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'login']);
    Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verify'])->name('verification.verify')->middleware(['auth:api', 'signed']);
    Route::post('/login', [AuthController::class, 'login']);
    // ->middleware(['verified']);
    Route::get('/users', [UsersController::class, 'users']);
    Route::get('/email/verify', function () {
        // Your email verification view logic here
    })->name('verification.notice');
    Route::middleware('auth:api')->group(function () {
        // Define your authenticated users routes here
        Route::get('/user/profile', [ProfileController::class, 'show']);
        Route::post('/logout', [AuthController::class, 'logout']);
        // Add more authenticated routes as needed
    });
    /* route for unauthorized token */
    Route::get('/unauthorized', function () {
        return ResponseHelper::error('Unauthorized', null, 401);
    })->name('unauthorized');



});


/* wallets */


Route::middleware('auth:api')->prefix('v1')->group(function () {
    // Wallet routes
    Route::resource('wallets', WalletController::class)->only(['index', 'store', 'show', 'update', 'destroy']);



    // Currency routes
    Route::resource('currencies', CurrencyController::class)->only(['index', 'store', 'show', 'update', 'destroy']);

    // Transaction routes
    Route::resource('transactions', TransactionController::class)->only(['index', 'store', 'show', 'update', 'destroy']);

    // Wallet Denomination routes
    Route::post('/wallets/attach-denomination', [WalletDenominationController::class, 'attach'])->name('wallets.attach-denomination');
    Route::delete('/wallets/detach-denomination', [WalletDenominationController::class, 'detach'])->name('wallets.detach-denomination');

    // Denomination routes
    Route::apiResource('currencies.denominations', DenominationController::class)
    ->except(['create', 'edit']);

    Route::delete('currencies/{currency}/denominations/{denomination}/force', [DenominationController::class, 'forceDestroy'])
    ->name('currencies.denominations.forceDestroy');

    Route::patch('currencies/{currency}/denominations/{denomination}/restore', [DenominationController::class, 'restore'])
    ->name('currencies.denominations.restore');

});


