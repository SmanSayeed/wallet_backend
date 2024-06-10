<?php

namespace Database\Factories;

use App\Models\Denomination;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

class DenominationFactory extends Factory
{
    protected $model = Denomination::class;

    public function definition()
    {
        return [
            'currency_id' => Currency::factory(),
            'title' => $this->faker->word,
            'amount' => $this->faker->randomFloat(2, 0.01, 1000),
        ];
    }
}
