<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Denomination extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'currency_id',
        'title',
        'amount',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
    public function wallets()
    {
        return $this->belongsToMany(Wallet::class, 'wallet_denomination', 'denomination_id', 'wallet_id')
                    ->withPivot('id', 'user_id', 'currency_id', 'amount')
                    ->withTimestamps();
    }
}
