<?php
namespace App\Services;

use App\Models\Wallet;
use App\Models\User;

class WalletService
{
    public function getAllWallets(User $user)
    {
        return $user->wallets()->with('currency')->get();
    }

    public function createWallet(User $user, array $data)
    {
        return $user->wallets()->create($data);
    }

    public function getWalletById(User $user, $id)
    {
        return $user->wallets()->with('currency')->find($id);
    }

    public function updateWallet(User $user, $id, array $data)
    {
        $wallet = $user->wallets()->find($id);

        if ($wallet) {
            $wallet->update($data);
        }

        return $wallet;
    }

    public function deleteWallet(User $user, $id)
    {
        $wallet = $user->wallets()->find($id);

        if ($wallet) {
            return $wallet->delete();
        }

        return false;
    }
}
