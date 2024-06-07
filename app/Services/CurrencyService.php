<?php
namespace App\Services;

use App\Models\Currency;

class CurrencyService
{
    public function getAllCurrencies()
    {
        return Currency::all();
    }

    public function createCurrency(array $data)
    {
        return Currency::create($data);
    }

    public function getCurrencyById($id)
    {
        return Currency::find($id);
    }

    public function updateCurrency($id, array $data)
    {
        $currency = Currency::find($id);

        if ($currency) {
            $currency->update($data);
        }

        return $currency;
    }

    public function deleteCurrency($id)
    {
        $currency = Currency::find($id);

        if ($currency) {
            return $currency->delete();
        }

        return false;
    }
}
