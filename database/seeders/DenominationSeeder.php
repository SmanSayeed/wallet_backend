<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;
use App\Models\Denomination;

class DenominationSeeder extends Seeder
{
    public function run()
    {
        $usd = Currency::where('code', 'USD')->first();
        $bdt = Currency::where('code', 'BDT')->first();

        $denominations = [
            [
                'currency_id' => $usd->id,
                'title' => 'One Dollar',
                'amount' => 1.00,
            ],
            [
                'currency_id' => $usd->id,
                'title' => 'Five Dollars',
                'amount' => 5.00,
            ],
            [
                'currency_id' => $usd->id,
                'title' => 'Ten Dollars',
                'amount' => 10.00,
            ],
            [
                'currency_id' => $bdt->id,
                'title' => 'One Taka',
                'amount' => 1.00,
            ],
            [
                'currency_id' => $bdt->id,
                'title' => 'Five Taka',
                'amount' => 5.00,
            ],
            [
                'currency_id' => $bdt->id,
                'title' => 'Ten Taka',
                'amount' => 10.00,
            ],
        ];

        foreach ($denominations as $denomination) {
            Denomination::create($denomination);
        }
    }
}
