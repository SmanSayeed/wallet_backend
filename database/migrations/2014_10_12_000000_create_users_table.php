<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->string('email')->unique()->nullable(false);
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone')->nullable(false);
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('address')->nullable(false);
            $table->string('country')->nullable(false);
            $table->string('post_code')->nullable(false);
            $table->string('ip_address')->nullable();
            $table->integer('login_attempts')->default(0);
            $table->boolean('status')->default(true);
            $table->boolean('is_freezed')->default(false);
            $table->timestamp('freezed_at')->nullable();
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->string('nid')->nullable(false);
            $table->string('profile_image')->nullable();
            $table->string('otp')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->string('remember_token')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
