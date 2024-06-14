<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [

        'name',
        'slug',
        'balance',
        'slug',
        'user_id',
        'currency_id',
        'deposited_balance'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function denominations()
    {
        return $this->belongsToMany(Denomination::class, 'wallet_denomination', 'wallet_id', 'denomination_id')
                    ->withPivot('id', 'user_id', 'currency_id', 'amount')
                    ->withTimestamps();
    }

    public function getDenominationPivot($denominationId, $pivotId)
    {
        return $this->denominations()->where('id', $denominationId)
                                     ->wherePivot('id', $pivotId)
                                     ->first();
    }


}
