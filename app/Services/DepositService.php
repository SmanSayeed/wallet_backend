<?php

namespace App\Services;

use App\Events\SendOtpEmailEvent;
use App\Helpers\ErrorHelper;
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

    public function getWalletDenominations($user,array $wallet_denomination_pivot_ids){

        return WalletDenomination::whereIn('id', $wallet_denomination_pivot_ids)
        ->where('is_deposited', false)
        ->where('user_id', $user->id)
        ->get();
    }

    public function createTransaction($user, $wallet, $currency, $totalAmount, $status, $otp)
    {
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'currency_id' => $currency->id,
            'currency_name' => $currency->name,
            'currency_symbol' => $currency->symbol,
            'type' => 'deposit',
            'amount' => $totalAmount,
            'payment_gateway' => 'Dummy Payment Gateway',
            'payment_gateway_status' => $status,
            'otp' => $otp,
            'otp_sent_at' => now(),
            'otp_expires_at' => now()->addMinutes(10), // OTP valid for 10 minutes
        ]);
        return $transaction;
    }


    public function calculateTotalAmount($wallet_denominations){
        $totalAmount = 0;
        foreach ($wallet_denominations as $wallet_denomination) {
            $totalAmount += $wallet_denomination->amount * $wallet_denomination->denomination->value;
        }
        return $totalAmount;
    }

    public function handleDepositAndSendOTP($user, array $wallet_denomination_pivot_ids)
    {
        return DB::transaction(function () use ($user, $wallet_denomination_pivot_ids) {
            // Fetch denominations and calculate total amount
            $wallet_denominations =$this->getWalletDenominations($user, $wallet_denomination_pivot_ids);

            if ($wallet_denominations->isEmpty()) {
                throw new \Exception('No valid denominations found.');
            }

            $totalAmount = $this->calculateTotalAmount($wallet_denominations);
            // Update wallet balances
            $wallet = Wallet::find($wallet_denominations->first()->wallet_id);
            /*
            $this->walletBalanceService->decrementWalletBalance($wallet, $totalAmount, 1);
            */
            // totalAmount is already calculated
            // Fetch currency details
            $currency = Currency::find($wallet_denominations->first()->currency_id);
            // Create a transaction record
            $otp = rand(100000, 999999); // Generate a random 6-digit OTP
            $transaction = $this->createTransaction($user,$wallet,$currency,$totalAmount,'pending',$otp);

            // -------------------Verify OTP before transacting----------------------
            event(new SendOtpEmailEvent($user, $otp));
            // Dispatch a job to process the payment asynchronously
            // ProcessPayment::dispatch($transaction, $wallet_denomination_pivot_ids, $currency->code);

            $transactionData = [
                'id' => $transaction->id,
            ];

            return $transactionData;
        });
    }


    public function verifyOtp($user, $transactionId, $otp, $wallet_denomination_pivot_ids)
    {
        // Verify OTP before starting the transaction
        $transaction = Transaction::where('id', $transactionId)
            ->where('user_id', $user->id)
            ->where('otp', $otp)
            ->where('otp_expires_at', '>', now())
            ->first();


            if (!$transaction) {
                throw new \Exception(ErrorHelper::otpVerificationFailed());
            }



        return DB::transaction(function () use ($transaction,$wallet_denomination_pivot_ids) {
            $transaction->update(['otp_verified_at' => now()]);

            $currencyCode = Currency::where('id', $transaction->currency_id)->value('code');

            // Proceed with the payment processing
            ProcessPayment::dispatch($transaction, $wallet_denomination_pivot_ids, $currencyCode,true);


            // Fetch the updated transaction after processing
            $updatedTransaction = Transaction::findOrFail($transaction->id);

            return $updatedTransaction;
        });

    }

}
