<?php

namespace App\Http\Controllers\Api\V1\Wallets;

use App\Http\Controllers\Controller;
use App\Services\WalletDenominationService;
use App\Helpers\ResponseHelper;
use App\Http\Requests\WalletDenominationRequest;
use Illuminate\Http\Request;

class WalletDenominationController extends Controller
{
    protected $walletDenominationService;

    public function __construct(WalletDenominationService $walletDenominationService)
    {
        $this->walletDenominationService = $walletDenominationService;
    }

    public function attach(WalletDenominationRequest $request)
    {
        $walletId = $request->input('wallet_id');
        $denominationId = $request->input('denomination_id');
        $walletDenomination = $this->walletDenominationService->attachDenomination($walletId, $denominationId);
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
