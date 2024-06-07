<?php
namespace App\Services;

use App\Models\Denomination;
use App\Models\Wallet;

class DenominationService
{
    public function getAllDenominations($walletId)
    {
        $wallet = Wallet::findOrFail($walletId);
        return $wallet->denominations;
    }

    public function createDenomination($walletId, array $data)
    {
        $wallet = Wallet::findOrFail($walletId);
        $denomination = new Denomination($data);
        $wallet->denominations()->save($denomination);
        $wallet->increment('balance', $denomination->amount);
        return $denomination;
    }

    public function getDenominationById($walletId, $id)
    {
        $wallet = Wallet::findOrFail($walletId);
        return $wallet->denominations()->find($id);
    }

    public function updateDenomination($walletId, $id, array $data)
    {
        $wallet = Wallet::findOrFail($walletId);
        $denomination = $wallet->denominations()->find($id);

        if ($denomination) {
            $wallet->decrement('balance', $denomination->amount);
            $denomination->update($data);
            $wallet->increment('balance', $denomination->amount);
        }

        return $denomination;
    }

    public function deleteDenomination($walletId, $id)
    {
        $wallet = Wallet::findOrFail($walletId);
        $denomination = $wallet->denominations()->find($id);

        if ($denomination) {
            $wallet->decrement('balance', $denomination->amount);
            return $denomination->delete();
        }

        return false;
    }
}
