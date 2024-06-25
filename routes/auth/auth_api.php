<?php

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Users\ProfileController;
use App\Http\Controllers\Api\V1\Users\UsersController;
use App\Http\Controllers\Api\V1\Wallets\CurrencyController;
use App\Http\Controllers\Api\V1\Wallets\DepositController;
use App\Http\Controllers\Api\V1\Wallets\TransactionController;
use App\Http\Controllers\Api\V1\Wallets\WalletDenominationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Wallets\WalletController;
use App\Http\Controllers\Api\V1\Wallets\DenominationController;

/*
|--------------------------------------------------------------------------
|Authenticated API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for application.
|
*/

/* ---------- Auth routes ---------- */
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/user/profile', [ProfileController::class, 'show']);
Route::get('/users', [UsersController::class, 'users']);

/*----------  Wallets  routes -------- */
Route::resource('wallets', WalletController::class)->only(['index', 'store', 'show', 'update', 'destroy']);

/*************** Wallet Denomination routes ****************/
Route::get('/wallets/{walletId}/get-denominations', [WalletDenominationController::class, 'getDenominations'])->name('wallets.get-denominations');

Route::post('/wallets/attach-denomination', [WalletDenominationController::class, 'attach'])->name('wallets.attach-denomination');

Route::post('/wallets/detach-denomination', [WalletDenominationController::class, 'detach'])->name('wallets.detach-denomination');


/*----------  currencies  routes -------- */
Route::resource('currencies', CurrencyController::class)->only(['index', 'store', 'show', 'update', 'destroy']);



/*----------  currencies denominations routes -------- */
Route::apiResource('currencies.denominations', DenominationController::class)->except(['create', 'edit']);
Route::delete('currencies/{currency}/denominations/{denomination}/force', [DenominationController::class, 'forceDestroy'])->name('currencies.denominations.forceDestroy');
Route::patch('currencies/{currency}/denominations/{denomination}/restore', [DenominationController::class, 'restore'])->name('currencies.denominations.restore');

/*---------- transactions -------- */
Route::resource('transactions', TransactionController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
Route::get('user/transactions', [TransactionController::class, 'userTransactions']);

/*------------- Deposite ----------- */
Route::post('make-deposit', [DepositController::class, 'makeDeposit'])->name('make.deposit');

Route::post('verify-transaction-otp', [DepositController::class, 'verifyTransactionOtp'])->name('verify-transaction-otp');




