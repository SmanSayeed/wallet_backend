<?php
namespace App\Http\Controllers\Api\V1\Wallets;

use App\Http\Controllers\Controller;
use App\Services\DenominationService;
use App\Helpers\ResponseHelper;
use App\Http\Requests\DenominationRequest;
use Illuminate\Http\Request;

class DenominationController extends Controller
{
    protected $denominationService;

    public function __construct(DenominationService $denominationService)
    {
        $this->denominationService = $denominationService;
    }

    public function index(Request $request, $walletId)
    {
        $denominations = $this->denominationService->getAllDenominations($walletId);
        return ResponseHelper::success('Denominations retrieved successfully', $denominations);
    }

    public function store(DenominationRequest $request, $walletId)
    {
        $denomination = $this->denominationService->createDenomination($walletId, $request->validated());
        return ResponseHelper::success('Denomination added successfully', $denomination, 201);
    }

    public function show(Request $request, $walletId, $id)
    {
        $denomination = $this->denominationService->getDenominationById($walletId, $id);

        if (!$denomination) {
            return ResponseHelper::error('Denomination not found', null, 404);
        }

        return ResponseHelper::success('Denomination retrieved successfully', $denomination);
    }

    public function update(DenominationRequest $request, $walletId, $id)
    {
        $denomination = $this->denominationService->updateDenomination($walletId, $id, $request->validated());

        if (!$denomination) {
            return ResponseHelper::error('Denomination not found', null, 404);
        }

        return ResponseHelper::success('Denomination updated successfully', $denomination);
    }

    public function destroy(Request $request, $walletId, $id)
    {
        $deleted = $this->denominationService->deleteDenomination($walletId, $id);

        if (!$deleted) {
            return ResponseHelper::error('Denomination not found', null, 404);
        }

        return ResponseHelper::success('Denomination removed successfully');
    }
}
