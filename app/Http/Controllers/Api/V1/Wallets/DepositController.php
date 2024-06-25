<?php

namespace App\Http\Controllers\Api\V1\Wallets;

use App\Http\Controllers\Controller;
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

    public function makeDeposit(DepositRequest $request)
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
            $result = $this->depositService->verifyOtp($user, $transactionId, $otp);
            return ResponseHelper::success('OTP verified successfully. Payment processing started.', $result);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 400);
        }
    }
}
