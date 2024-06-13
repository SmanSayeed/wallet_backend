<?php

use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test-email', function () {
    $user = User::find(1); // Make sure to replace with a valid user ID

    try {
        Mail::to($user->email)->send(new VerifyEmail($user));
        return 'Email sent successfully!';
    } catch (\Exception $e) {
        Log::error('Failed to send test email', [
            'error' => $e->getMessage(),
        ]);
        return 'Failed to send email';
    }
});
