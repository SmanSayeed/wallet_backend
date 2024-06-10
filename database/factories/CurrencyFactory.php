<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    public function definition()
    {
        return [
            'name' => $this->faker->currencyCode,
            'code' => $this->faker->currencyCode,
            'symbol' => $this->faker->randomElement(['$', '€', '£', '¥', '₹']),
        ];
    }
}
