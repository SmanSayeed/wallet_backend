<?php
namespace App\Services;

use App\Models\Currency;
use App\Models\Wallet;
use App\Models\Denomination;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class WalletDenominationService
{

    protected $walletBalanceService;

    public function __construct(WalletBalanceService $walletBalanceService)
    {
        $this->walletBalanceService = $walletBalanceService;
    }

    public function attachDenomination($userId, $currencyId, $walletId, $denominationId, $amount)
    {
        $wallet = Wallet::findOrFail($walletId);
        $denomination = Denomination::findOrFail($denominationId);
        $wallet->denominations()->attach($denominationId, [
            'amount' => $amount,
            'user_id' => $userId,
            'currency_id' => $currencyId
        ]);
        $wallet->increment('balance', $denomination->amount * $amount);
        $attachedDenomination = $wallet->denominations()->withPivot('amount')->find($denominationId);
        return $attachedDenomination;
    }


    public function detachDenomination($walletId, $denominationPivotId, $userId)
    {
        // dump("Detaching denomination - Wallet ID: $walletId, Denomination Pivot ID: $denominationPivotId, User ID: $userId");
        $wallet = Wallet::where('user_id', $userId)->findOrFail($walletId);
        // dump("Wallet found: " . $wallet->id);
        $denominationPivot = $wallet->denominations()->wherePivot('id', $denominationPivotId)->first();
        if ($denominationPivot) {
            // dump("Denomination pivot found: " . $denominationPivot->id);
            // Decrement the wallet balance by the amount in the pivot entry
            $pivotAmount = (float) $denominationPivot->pivot->amount;
            $denominationValue = (float) $denominationPivot->amount;
            $wallet->decrement('balance',$pivotAmount* $denominationValue );
            $pivotTableName = $wallet->denominations()->getTable();
            try {
                // Detach the specific pivot entry
                // $wallet->denominations()->detach($denominationPivotId);
                $deleted = DB::table($pivotTableName)
                    ->where('id', $denominationPivotId)
                    ->delete();

                if ($deleted) {
                    // Optionally handle any other logic after deletion
                    return true;
                } else {
                    // Handle case where deletion fails
                    return false;
                }
            } catch (\Exception $e) {
                \Log::error('Error detaching denomination: ' . $e->getMessage());
                return false;
            }

        } else {
            \Log::error("Denomination pivot not found");
        }

        return false;

    }

}
