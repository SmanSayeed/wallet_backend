<?php

namespace App\Http\Controllers\Api\V1\Wallets;

use App\Http\Controllers\Controller;
use App\Services\WalletDenominationService;
use App\Helpers\ResponseHelper;
use App\Http\Requests\WalletDenominationRequest;
use App\Models\Denomination;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletDenominationController extends Controller
{
    protected $walletDenominationService;

    public function __construct(WalletDenominationService $walletDenominationService)
    {
        $this->walletDenominationService = $walletDenominationService;
    }

    public function getDenominations($walletId)
    {
        $wallet = Wallet::with('denominations')->findOrFail($walletId);
        return ResponseHelper::success('Denominations retrieved successfully', $wallet->denominations);
    }

    public function attach(WalletDenominationRequest $request)
    {
        $userId = $request->user()->id;
        $currencyId = $request->input('currency_id');
        $walletId = $request->input('wallet_id');
        $denominationId = $request->input('denomination_id');
        $amount = $request->input('amount', 1); // Default amount to 1 if not provided
        $walletDenomination = $this->walletDenominationService->attachDenomination($userId, $currencyId, $walletId, $denominationId, $amount);
        return ResponseHelper::success('Denomination attached to wallet successfully', $walletDenomination, 201);
    }

    public function detach(WalletDenominationRequest $request)
    {
        $walletId = $request->input('wallet_id');
        $denominationId = $request->input('denomination_id');
        $deleted = $this->walletDenominationService->detachDenomination($walletId, $denominationId);

        if (!$deleted) {
            return ResponseHelper::error('Denomination not found in wallet', null, 404);
        }

        return ResponseHelper::success('Denomination detached from wallet successfully');
    }
}
