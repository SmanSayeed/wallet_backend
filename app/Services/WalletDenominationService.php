<?php
namespace App\Services;

use App\Models\Currency;
use App\Models\Wallet;
use App\Models\Denomination;
use App\Models\User;

class WalletDenominationService
{
    public function attachDenomination($userId, $currencyId, $walletId, $denominationId, $amount)
    {
        $wallet = Wallet::findOrFail($walletId);
        $denomination = Denomination::findOrFail($denominationId);

        // Attach the denomination with additional attributes
        $wallet->denominations()->attach($denominationId, [
            'amount' => $amount,
            'user_id' => $userId,
            'currency_id' => $currencyId
        ]);

        // Increment wallet balance by the denomination amount multiplied by the quantity
    $wallet->increment('balance', $denomination->amount * $amount);

    // Retrieve the attached denomination with pivot data
    $attachedDenomination = $wallet->denominations()->withPivot('amount')->find($denominationId);

    // Return the attached denomination with pivot data
    return $attachedDenomination;
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
