<?php

namespace App\Services;

use App\Models\Denomination;
use App\Models\Currency;

class DenominationService
{
    public function getAllDenominations($currencyId)
    {
        $currency = Currency::findOrFail($currencyId);
        return $currency->denominations;
    }

    public function createDenomination($currencyId, array $data)
    {
        $currency = Currency::findOrFail($currencyId);
        $data['currency_id'] = $currency->id;
        return Denomination::create($data);
    }

    public function getDenominationById($currencyId, $id)
    {
        $currency = Currency::findOrFail($currencyId);
        return $currency->denominations()->find($id);
    }

    public function updateDenomination($currencyId, $id, array $data)
    {
        $currency = Currency::findOrFail($currencyId);
        $denomination = $currency->denominations()->find($id);

        if ($denomination) {
            $denomination->update($data);
        }

        return $denomination;
    }

    public function deleteDenomination($currencyId, $id)
    {
        $currency = Currency::findOrFail($currencyId);
        $denomination = $currency->denominations()->find($id);

        if ($denomination) {
            return $denomination->delete();
        }

        return false;
    }

    public function forceDeleteDenomination($currencyId, $id)
    {
        $currency = Currency::findOrFail($currencyId);
        $denomination = $currency->denominations()->withTrashed()->find($id);

        if ($denomination) {
            return $denomination->forceDelete();
        }

        return false;
    }

    public function restoreDenomination($currencyId, $id)
    {
        $currency = Currency::findOrFail($currencyId);
        $denomination = $currency->denominations()->onlyTrashed()->find($id);

        if ($denomination) {
            return $denomination->restore();
        }

        return false;
    }
}
