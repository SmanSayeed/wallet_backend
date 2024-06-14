<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletDenomination extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'currency_id',
        'wallet_id',
        'denomination_id',
        'amount',
        'is_deposited',
        'is_withdraw',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function denomination()
    {
        return $this->belongsTo(Denomination::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
