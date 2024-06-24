<?php

namespace App\Services;

use App\Jobs\ProcessPayment;
use App\Models\Wallet;
use App\Models\WalletDenomination;
use App\Models\Transaction;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;

class DepositService
{
    protected $walletBalanceService;

    public function __construct(WalletBalanceService $walletBalanceService)
    {
        $this->walletBalanceService = $walletBalanceService;
    }

    public function handleDeposit($user, array $wallet_denomination_pivot_ids)
    {
        return DB::transaction(function () use ($user, $wallet_denomination_pivot_ids) {
            // Fetch denominations and calculate total amount
            $wallet_denominations = WalletDenomination::whereIn('id', $wallet_denomination_pivot_ids)
                ->where('is_deposited', false)
                ->where('user_id', $user->id)
                ->get();

            if ($wallet_denominations->isEmpty()) {
                throw new \Exception('No valid denominations found.');
            }
            $totalAmount = $this->calculateTotalAmount($wallet_denominations);
            // Update wallet balances
            $wallet = Wallet::find($wallet_denominations->first()->wallet_id);
            $this->walletBalanceService->decrementWalletBalance($wallet, $totalAmount, 1); // totalAmount is already calculated
            // Fetch currency details
            $currency = Currency::find($wallet_denominations->first()->currency_id);
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
            ProcessPayment::dispatch($transaction, $wallet_denomination_pivot_ids, $currency->code);

            return $transaction;
        });
    }

    public function calculateTotalAmount($wallet_denominations){
        $totalAmount = 0;
        foreach ($wallet_denominations as $wallet_denomination) {
            $totalAmount += $wallet_denomination->amount * $wallet_denomination->denomination->value;
        }
        return $totalAmount;
    }
}
