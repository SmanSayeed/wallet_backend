<?php

namespace App\Http\Controllers\Api\V1\Wallets;

use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use App\Helpers\ResponseHelper;
use App\Http\Requests\TransactionRequest;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        $transactions = $this->transactionService->getAllTransactions($request->user());
        return ResponseHelper::success('Transactions retrieved successfully', $transactions);
    }

    public function store(TransactionRequest $request)
    {
        $transaction = $this->transactionService->createTransaction($request->user(), $request->validated());
        return ResponseHelper::success('Transaction created successfully', $transaction, 201);
    }

    public function show(Request $request, $id)
    {
        $transaction = $this->transactionService->getTransactionById($request->user(), $id);

        if (!$transaction) {
            return ResponseHelper::error('Transaction not found', null, 404);
        }

        return ResponseHelper::success('Transaction retrieved successfully', $transaction);
    }

    public function update(TransactionRequest $request, $id)
    {
        $transaction = $this->transactionService->updateTransaction($request->user(), $id, $request->validated());

        if (!$transaction) {
            return ResponseHelper::error('Transaction not found', null, 404);
        }

        return ResponseHelper::success('Transaction updated successfully', $transaction);
    }

    public function destroy(Request $request, $id)
    {
        $deleted = $this->transactionService->deleteTransaction($request->user(), $id);

        if (!$deleted) {
            return ResponseHelper::error('Transaction not found', null, 404);
        }

        return ResponseHelper::success('Transaction deleted successfully');
    }
}
