<?php

namespace App\Jobs;

use App\Events\PaymentFailureEvent;
use App\Events\PaymentSuccessEvent;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\WalletDenomination;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ProcessPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transaction;
    protected $denominationIds;
    protected $currencyCode;
    public function __construct(Transaction $transaction, array $denominationIds,$currencyCode)
    {
        $this->transaction = $transaction;
        $this->denominationIds = $denominationIds;
        $this->currencyCode = $currencyCode;
    }

    public function handle()
    {
        // Dummy payment gateway API call
        $response = Http::post('https://dummy-payment-gateway/api/process', [
            'amount' => $this->transaction->amount,
            'currency' => $this->currencyCode
        ]);

        if ($response->successful()) {
            $this->transaction->update(['payment_gateway_status' => 'success']);
            $this->processDeposit();
        } else {
            // Update payment status
            $this->transaction->update(['payment_gateway_status' => 'failed']); 
            // Trigger failure event
            event(new PaymentFailureEvent($this->transaction));
        }
    }

    protected function processDeposit()
    {
        $user = $this->transaction->user;
        $denominationIds = $this->denominationIds;
        // Fetch denominations
        $denominations = WalletDenomination::whereIn('id', $denominationIds)
            ->where('is_deposited', false)
            ->where('user_id', $user->id)
            ->get();
        // Calculate total amount
        $totalDepositAmount = 0;
        foreach ($denominations as $denomination) {
            $totalDepositAmount += $denomination->amount * $denomination->denomination->value;
        }
        // Update wallet balances
        $wallet = Wallet::find($denominations->first()->wallet_id);
        $wallet->balance -= $totalDepositAmount;
        $wallet->deposited_balance += $totalDepositAmount;
        $wallet->save();
        // Update denominations as deposited
        WalletDenomination::whereIn('id', $denominationIds)->update(['is_deposited' => true]);
        // Trigger success event
        event(new PaymentSuccessEvent($this->transaction));
    }
}
