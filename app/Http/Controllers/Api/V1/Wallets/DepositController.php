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
            $result = $this->depositService->handleDeposit($user, $wallet_denomination_pivot_ids);
            return ResponseHelper::success('Deposit created successfully', $result);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 400);
        }
    }
}
