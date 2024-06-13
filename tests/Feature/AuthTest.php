<?php
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Carbon;

// Imports necessary for the test
use function Pest\Laravel\post;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Faker\fake;

beforeEach(function () {
    Notification::fake();
    Event::fake();
});

it('can register a new user', function () {
    $userData = [
        'name' => fake()->name,
        'email' => fake()->email,
        'password' => 'password',
        'password_confirmation' => 'password',
        'phone' => fake()->phoneNumber,
        'address' => fake()->address,
        'country' => fake()->country,
        'post_code' => fake()->postcode,
        'nid' => fake()->uuid,
    ];

    post('/api/v1/register', $userData)
        ->assertStatus(201)
        ->assertJsonStructure(['message', 'data']);

    assertDatabaseHas('users', [
        'email' => $userData['email'],
    ]);
});

it('sends otp after successful login', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password')
    ]);

    $credentials = ['email' => $user->email, 'password' => 'password'];

    post('/api/v1/login', $credentials)
        ->assertStatus(200)
        ->assertJsonPath('message', 'OTP sent successfully. Please verify your OTP.');
});

it('fails to send otp for invalid credentials', function () {
    $user = User::factory()->create();

    $credentials = ['email' => $user->email, 'password' => 'wrongpassword'];

    post('/api/v1/login', $credentials)
        ->assertStatus(401)
        ->assertJsonPath('message', 'Invalid credentials');
});

it('verifies otp successfully', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
        'otp' => '123456',
        'otp_expires_at' => Carbon::now()->addMinutes(5),
    ]);

    $credentials = ['email' => $user->email, 'password' => 'password'];
    post('/api/v1/login', $credentials);

    $otpData = ['email' => $user->email, 'otp' => '123456'];

    post('/api/v1/verify-otp', $otpData)
        ->assertStatus(200)
        ->assertJsonPath('message', 'OTP verified and user authenticated successfully.');
});

it('fails when otp is invalid or expired', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
        'otp' => '123456',
        'otp_expires_at' => Carbon::now()->subMinutes(5),
    ]);

    $otpData = ['email' => $user->email, 'otp' => '000000'];

    post('/api/v1/verify-otp', $otpData)
        ->assertStatus(400)
        ->assertJsonPath('message', 'Invalid or expired OTP.');
});
