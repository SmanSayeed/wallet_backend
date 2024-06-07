<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class WalletService
{
    public function getAllWallets(User $user)
    {
        return $user->wallets()->get();
    }

    private function checkIfSameUser($user_id){
        return Auth::user()->id==$user_id;
    }

    public function createWallet(User $user, array $data)
    {
        if (!$this->checkIfSameUser($data->user_id)) {
            return false;
        }
        return $user->wallets()->create($data);
    }

    public function getWalletById(User $user, $walletId)
    {
        return $user->wallets()->find($walletId);
    }

    public function updateWallet(User $user, $walletId, array $data)
    {
        $wallet = $user->wallets()->find($walletId);

        if ($wallet) {
            $wallet->update($data);
        }

        return $wallet;
    }

    public function deleteWallet(User $user, $walletId)
    {
        $wallet = $user->wallets()->find($walletId);

        if ($wallet) {
            $wallet->delete();
            return true;
        }

        return false;
    }
}
