<?php

namespace App\Http\Controllers\Api\V1\Wallets;

use App\Http\Controllers\Controller;
use App\Services\WalletService;
use App\Helpers\ResponseHelper;
use App\Http\Requests\WalletRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;
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

    public function store(WalletRequest $request): JsonResponse
    {
        try {
            if(Auth::user()->id!=$request->user()->id){
                return ResponseHelper::error('Unauthorized', null, 401);
            }
            $wallet = $this->walletService->createWallet($request->user(), $request->validated());
            return ResponseHelper::success('Wallet created successfully', $wallet, 201);
        } catch (Throwable $e) {
            // Log the error if needed
            Log::error($e);
            // Return a JSON response with the error message
            return ResponseHelper::error('An error occurred while creating the wallet.', null, 500);
        }
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
