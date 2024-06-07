<?php

namespace App\Http\Controllers\Api\V1\Wallets;

use App\Http\Controllers\Controller;
use App\Services\WalletService;
use App\Helpers\ResponseHelper;
use App\Http\Requests\WalletRequest;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function index(Request $request)
    {
        $wallets = $this->walletService->getAllWallets($request->user());
        return ResponseHelper::success('Wallets retrieved successfully', $wallets);
    }

    public function store(WalletRequest $request)
    {
        $wallet = $this->walletService->createWallet($request->user(), $request->validated());
        return ResponseHelper::success('Wallet created successfully', $wallet, 201);
    }

    public function show(Request $request, $id)
    {
        $wallet = $this->walletService->getWalletById($request->user(), $id);

        if (!$wallet) {
            return ResponseHelper::error('Wallet not found', null, 404);
        }

        return ResponseHelper::success('Wallet retrieved successfully', $wallet);
    }

    public function update(WalletRequest $request, $id)
    {
        $wallet = $this->walletService->updateWallet($request->user(), $id, $request->validated());

        if (!$wallet) {
            return ResponseHelper::error('Wallet not found', null, 404);
        }

        return ResponseHelper::success('Wallet updated successfully', $wallet);
    }

    public function destroy(Request $request, $id)
    {
        $deleted = $this->walletService->deleteWallet($request->user(), $id);

        if (!$deleted) {
            return ResponseHelper::error('Wallet not found', null, 404);
        }

        return ResponseHelper::success('Wallet deleted successfully');
    }
}
