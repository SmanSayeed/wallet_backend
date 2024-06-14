<?php

namespace App\Http\Controllers\Api\V1\Wallets;

use App\Http\Controllers\Controller;
use App\Http\Requests\RemoveWalletDenominationRequest;
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
        $denominations = $wallet->denominations->map(function ($denomination) {
            return [
                'id' => $denomination->id,
                'title' => $denomination->title,
                'amount' => $denomination->amount, // value of denomination
                'pivot' => $denomination->pivot,
                'pivot_id'=>$denomination->pivot->id,
            ];
        });
        return ResponseHelper::success('Denominations retrieved successfully', $denominations);
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

    public function detach(RemoveWalletDenominationRequest $request)
    {
        try {
            $userId = $request->user_id;
            if($userId!=$request->user()->id){
                return ResponseHelper::error('Not Authorized ', null, 500);
            }
            $walletId = $request->input('wallet_id');
            $denominationPivotId = $request->input('denomination_pivot_id');
            $deleted = $this->walletDenominationService->detachDenomination($walletId, $denominationPivotId, $userId);
            if (!$deleted) {
                return ResponseHelper::error('Failed to detach denomination', null, 404);
            }
            return ResponseHelper::success('Denomination detached from wallet successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('An error occurred: ' . $e->getMessage(), null, 500);
        }
    }
}
