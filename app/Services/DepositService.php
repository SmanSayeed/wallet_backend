<?php

namespace App\Services;

use App\Events\PaymentSuccessEvent;
use App\Events\PaymentFailureEvent;
use App\Jobs\ProcessPayment;
use App\Models\Wallet;
use App\Models\WalletDenomination;
use App\Models\Transaction;
use App\Models\Currency; // Import the Currency model
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DepositService
{
    public function makeDeposit($user, array $denominationIds)
    {
        return DB::transaction(function () use ($user, $denominationIds) {
            // Fetch denominations and calculate total amount
            $denominations = WalletDenomination::whereIn('id', $denominationIds)
                ->where('is_deposited', false)
                ->where('user_id', $user->id)
                ->get();

            if ($denominations->isEmpty()) {
                throw new \Exception('No valid denominations found.');
            }

            $totalAmount = 0;
            foreach ($denominations as $denomination) {
                $totalAmount += $denomination->amount * $denomination->denomination->value;
            }

            // Update wallet balances
            $wallet = Wallet::find($denominations->first()->wallet_id);
            $wallet->balance -= $totalAmount;
            $wallet->deposited_balance += $totalAmount;
            $wallet->save();

            // Fetch currency details
            $currency = Currency::find($denominations->first()->currency_id);

            // Create a transaction record
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'currency_id' => $currency->id,
                'currency_name' => $currency->name, // Use currency name from the Currency model
                'currency_symbol' => $currency->symbol, // Use currency symbol from the Currency model
                'type' => 'deposit',
                'amount' => $totalAmount,
                'payment_gateway' => 'Dummy Payment Gateway', // Replace with actual payment gateway used
                'payment_gateway_status' => 'pending',
            ]);

            // Dispatch a job to process the payment asynchronously
            ProcessPayment::dispatch($transaction, $denominationIds,$currency->code);

            return $transaction;
        });
    }
}
