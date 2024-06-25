<?php

namespace App\Http\Controllers\Api\V1\Wallets;

use App\Helpers\ErrorHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\OtpTransactionRequest;
use App\Services\DepositService;
use App\Helpers\ResponseHelper;
use App\Http\Requests\DepositRequest;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    protected $depositService;

    public function __construct(DepositService $depositService)
    {
        // Typo here: this should be $this->depositService
        $this->depositService = $depositService;
    }

    public function makeDepositAndSendOtp(DepositRequest $request)
    {
        // dd($request);
        try {
            $user = $request->user();
            $wallet_denomination_pivot_ids = $request->input('wallet_denomination_pivot_ids');
            $result = $this->depositService->handleDepositAndSendOTP($user, $wallet_denomination_pivot_ids);
            // return ResponseHelper::success('Deposit created successfully', $result);
            return ResponseHelper::success('OTP sent successfully. Please verify OTP to proceed.', $result);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 400);
        }
    }


    public function verifyTransactionOtp(Request $request)
    {
        try {
            $user = $request->user();
            $transactionId = $request->input('transaction_id');
            $otp = $request->input('otp');
            $wallet_denomination_pivot_ids = $request->input('wallet_denomination_pivot_ids');

            $updatedTransaction = $this->depositService->verifyOtp($user, $transactionId, $otp, $wallet_denomination_pivot_ids);

            // Check payment status in the updated transaction
            $paymentStatus = $updatedTransaction->payment_gateway_status;

            switch ($updatedTransaction->payment_gateway_status) {
                case 'success':
                    return ResponseHelper::success('Payment processed successfully.', $updatedTransaction);
                case 'failed':
                    return ResponseHelper::error('Payment processing failed.', 400);
                case 'cancelled':
                    return ResponseHelper::error('Payment cancelled.', 400);
                default:
                    return ResponseHelper::error('Payment status is pending.', 400);
            }
        } catch (\Exception $e) {
          // Check if the exception is due to OTP verification failure
          if ($e->getMessage() === ErrorHelper::otpVerificationFailed()) {
            return ResponseHelper::error($e->getMessage(), 400);
        }

        // Handle other exceptions
        return ResponseHelper::error('An error occurred during payment processing.', 400);
   }
    }
}
