<?php
namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;

class TransactionService
{
    public function getAllTransactions(User $user)
    {
        return $user->transactions()->with('wallet')->get();
    }

    public function createTransaction(User $user, array $data)
    {
        $wallet = $user->wallets()->find($data['wallet_id']);

        if ($data['type'] === 'withdraw' && $wallet->balance < $data['amount']) {
            return null;
        }

        $transaction = $user->transactions()->create($data);

        if ($data['type'] === 'add') {
            $wallet->increment('balance', $data['amount']);
        } else {
            $wallet->decrement('balance', $data['amount']);
        }

        return $transaction;
    }

    public function getTransactionById(User $user, $id)
    {
        return $user->transactions()->with('wallet')->find($id);
    }

    public function updateTransaction(User $user, $id, array $data)
    {
        $transaction = $user->transactions()->find($id);

        if ($transaction) {
            $transaction->update($data);
        }

        return $transaction;
    }

    public function deleteTransaction(User $user, $id)
    {
        $transaction = $user->transactions()->find($id);

        if ($transaction) {
            $wallet = $transaction->wallet;
            if ($transaction->type === 'add') {
                $wallet->decrement('balance', $transaction->amount);
            } else {
                $wallet->increment('balance', $transaction->amount);
            }
            return $transaction->delete();
        }

        return false;
    }
}
