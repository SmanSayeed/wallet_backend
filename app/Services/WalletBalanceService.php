<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\Denomination;
use Illuminate\Support\Facades\DB;

class WalletBalanceService
{
    public function addDenominationToWallet($walletId, $denominationId, $amount, $userId, $currencyId)
    {
        $wallet = Wallet::findOrFail($walletId);
        $denomination = Denomination::findOrFail($denominationId);
        $wallet->denominations()->attach($denominationId, [
            'amount' => $amount,
            'user_id' => $userId,
            'currency_id' => $currencyId
        ]);

        $this->incrementWalletBalance($wallet, $denomination, $amount);
        return $wallet->denominations()->withPivot('amount')->find($denominationId);
    }

    public function removeDenominationFromWallet($walletId, $denominationPivotId, $userId)
    {
        $wallet = Wallet::where('user_id', $userId)->findOrFail($walletId);
        $denominationPivot = $wallet->denominations()->wherePivot('id', $denominationPivotId)->firstOrFail();

        $pivotAmount = (float) $denominationPivot->pivot->amount;
        $denominationValue = (float) $denominationPivot->amount;

        $this->decrementWalletBalance($wallet, $pivotAmount, $denominationValue);

        $pivotTableName = $wallet->denominations()->getTable();

        return DB::table($pivotTableName)
            ->where('id', $denominationPivotId)
            ->delete();
    }

    private function incrementWalletBalance(Wallet $wallet, Denomination $denomination, $amount)
    {
        $wallet->increment('balance', $denomination->amount * $amount);
    }

    private function decrementWalletBalance(Wallet $wallet, $pivotAmount, $denominationValue)
    {
        $wallet->decrement('balance', $pivotAmount * $denominationValue);
    }
}
