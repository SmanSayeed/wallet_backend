<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    public function run()
    {
        $currencies = [
            [
                'name' => 'Dollar',
                'code' => 'USD',
                'symbol' => '$'
            ],
            [
                'name' => 'Taka',
                'code' => 'BDT',
                'symbol' => '৳'
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
