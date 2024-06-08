<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => "Saadman",
            'email' => "786saadman@gmail.com",
            'email_verified_at' => now(),
            'password' => Hash::make('11112222'),
            'phone' => "01515691480",
            'address' => "Dhaka",
            'country' => "Bangladesh",
            'post_code' => "1230",
            'ip_address' => "127.212.12.45",
            'login_attempts' => 0,
            'status' => true,
            'role' => 'user',
            'nid' => "123123",
            'profile_image' => "Img1.jpg",
            'remember_token' => Str::random(10),
        ]);

        User::factory()->count(10)->create(); // Adjust the count as needed
    }
}
