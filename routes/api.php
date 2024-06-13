<?php

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Users\ProfileController;
use App\Http\Controllers\Api\V1\Users\UsersController;
use App\Http\Controllers\Api\V1\Wallets\CurrencyController;
use App\Http\Controllers\Api\V1\Wallets\TransactionController;
use App\Http\Controllers\Api\V1\Wallets\WalletDenominationController;
use App\Http\Controllers\DataController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
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
|
*/
Route::post('/process-data', [DataController::class, 'processData']);

// Version 1 API routes
Route::prefix('v1')->group(function () {
    Route::get('/', function () {
        return response()->json(['message' => 'Welcome to the Moby API.']);
    });

    // Auth routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/validate-otp', [AuthController::class, 'validateOtp']);


    Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        // Email verification routes


        Route::post('/email/verification-notification', function (Request $request) {
            $request->user()->sendEmailVerificationNotification();
            return response()->json(['message' => 'Verification link sent!']);
        })->middleware('throttle:6,1')->name('verification.send');

        Route::get('/email/verify', function () {
            return response()->json(['message' => 'Your email address is not verified.']);
        })->name('verification.notice');


        // Authenticated and Verified routes

        // Route::middleware('verified')
        // ->group(function ()
        // {
            // User routes
            Route::get('/user/profile', [ProfileController::class, 'show']);

            Route::get('/users', [UsersController::class, 'users']);



            // Wallets and Transactions routes
            Route::resource('wallets', WalletController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::resource('currencies', CurrencyController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
            Route::resource('transactions', TransactionController::class)->only(['index', 'store', 'show', 'update', 'destroy']);

            // Wallet Denomination routes
            Route::get('/wallets/{walletId}/get-denominations', [WalletDenominationController::class, 'getDenominations'])->name('wallets.get-denominations');
            Route::post('/wallets/attach-denomination', [WalletDenominationController::class, 'attach'])->name('wallets.attach-denomination');
            Route::delete('/wallets/detach-denomination', [WalletDenominationController::class, 'detach'])->name('wallets.detach-denomination');

            // Denomination routes
            Route::apiResource('currencies.denominations', DenominationController::class)->except(['create', 'edit']);
            Route::delete('currencies/{currency}/denominations/{denomination}/force', [DenominationController::class, 'forceDestroy'])->name('currencies.denominations.forceDestroy');
            Route::patch('currencies/{currency}/denominations/{denomination}/restore', [DenominationController::class, 'restore'])->name('currencies.denominations.restore');

            // User Transactions
            Route::get('user/transactions', [TransactionController::class, 'userTransactions']);
        // });
    });

    // Route for unauthorized token
    Route::get('/unauthorized', function () {
        return ResponseHelper::error('Unauthorized', null, 401);
    })->name('unauthorized');
});
