<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
    ];

    public function denominations()
    {
        return $this->hasMany(Denomination::class);
    }
}
