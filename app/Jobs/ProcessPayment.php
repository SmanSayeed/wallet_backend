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
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
class ProcessPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $transaction;
    protected $denominationIds;
    protected $currencyCode;
    protected $simulateSuccess;
    public function __construct(Transaction $transaction, array $denominationIds,$currencyCode,bool $simulateSuccess = true)
    {
        $this->transaction = $transaction;
        $this->denominationIds = $denominationIds;
        $this->currencyCode = $currencyCode;
        $this->simulateSuccess = $simulateSuccess;
    }

    public function handle()
    {
        // Dummy payment gateway API call

        /* Payment gateway part
        $response = Http::post(Config::get('payment.gateway_url'), [
            'amount' => $this->transaction->amount,
            'currency' => $this->currencyCode
        ]);
        */

        // $response = Http::post('https://api.stripe.com/v1/payments', [
        //     'amount' => $this->transaction->amount,
        //     'currency' => $this->currencyCode
        // ]);

        // Log::info('Payment Gateway Response:', ['response' => $response->json()]);

        // if ($response->successful()) {

        if($this->simulateSuccess) {
            $this->transaction->update(['payment_gateway_status' => 'success']);
            $this->processDeposit();
        } else {
            // Update payment status
            $this->transaction->update(['payment_gateway_status' => 'failed']);
            // Trigger failure event
            event(new PaymentFailureEvent($this->transaction));
        }

        // Fetch the updated transaction after processing
        $updatedTransaction = Transaction::findOrFail($this->transaction->id);

        // Trigger success event with the updated transaction details
        event(new PaymentSuccessEvent($updatedTransaction));
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
    }
}
