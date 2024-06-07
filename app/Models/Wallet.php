<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'balance',
        'currency_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Other relationships and methods...
}
