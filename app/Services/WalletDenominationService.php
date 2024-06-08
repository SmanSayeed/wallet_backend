<?php
namespace App\Services;

use App\Models\Wallet;
use App\Models\Denomination;

class WalletDenominationService
{
    public function attachDenomination($walletId, $denominationId)
    {
        $wallet = Wallet::findOrFail($walletId);
        $denomination = Denomination::findOrFail($denominationId);
        $wallet->denominations()->attach($denominationId);
        $wallet->increment('balance', $denomination->amount);
        return $wallet->denominations()->where('denomination_id', $denominationId)->first();
    }

    public function detachDenomination($walletId, $denominationId)
    {
        $wallet = Wallet::findOrFail($walletId);
        $denomination = $wallet->denominations()->where('denomination_id', $denominationId)->first();

        if ($denomination) {
            $wallet->decrement('balance', $denomination->amount);
            $wallet->denominations()->detach($denominationId);
            return true;
        }

        return false;
    }
}
