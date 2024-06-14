<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'wallet_id',
        'currency_id',
        'currency_name',
        'currency_symbol',
        'type',
        'amount',
        'payment_gateway',
        'payment_gateway_status',
        'otp',
        'otp_sent_at',
        'otp_verified_at',
        'otp_expires_at',
    ];


    protected $casts = [
        'otp_expires_at' => 'datetime',
    ];
}
