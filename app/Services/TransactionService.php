<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    public function getAllTransactions(User $user)
    {
        return Transaction::where('user_id', $user->id)->get();
    }

    public function createTransaction(User $user, array $data)
    {
        // Simulate payment gateway interaction
        $this->processPaymentGateway($data);

        // Create the transaction
        $transaction = new Transaction($data);
        $transaction->user_id = $user->id;
        $transaction->save();

        return $transaction;
    }

    public function getTransactionById(User $user, $id)
    {
        return Transaction::where('user_id', $user->id)->find($id);
    }

    public function updateTransaction(User $user, $id, array $data)
    {
        $transaction = Transaction::where('user_id', $user->id)->find($id);

        if ($transaction) {
            $transaction->update($data);
        }

        return $transaction;
    }

    public function deleteTransaction(User $user, $id)
    {
        $transaction = Transaction::where('user_id', $user->id)->find($id);

        if ($transaction) {
            return $transaction->delete();
        }

        return false;
    }

    public function getUserTransactions($userId)
    {
        return Transaction::where('user_id', $userId)->get();
    }

    private function processPaymentGateway($data)
    {
        // Dummy payment gateway processing logic
        Log::info('Processing payment gateway with data:', $data);

        // Simulate payment success or failure
        if (rand(0, 1) == 1) {
            return true;
        } else {
            throw new \Exception('Payment gateway error.');
        }
    }
}
