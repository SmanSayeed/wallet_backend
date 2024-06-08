<?php

namespace App\Http\Controllers\Api\V1\Wallets;

use App\Http\Controllers\Controller;
use App\Services\DenominationService;
use App\Helpers\ResponseHelper;
use App\Http\Requests\DenominationRequest;

class DenominationController extends Controller
{
    protected $denominationService;

    public function __construct(DenominationService $denominationService)
    {
        $this->denominationService = $denominationService;
    }

    public function index($currencyId)
    {
        $denominations = $this->denominationService->getAllDenominations($currencyId);
        return ResponseHelper::success('Denominations retrieved successfully', $denominations);
    }

    public function store(DenominationRequest $request, $currencyId)
    {
        $denomination = $this->denominationService->createDenomination($currencyId, $request->validated());
        return ResponseHelper::success('Denomination created successfully', $denomination, 201);
    }

    public function show($currencyId, $id)
    {
        $denomination = $this->denominationService->getDenominationById($currencyId, $id);

        if (!$denomination) {
            return ResponseHelper::error('Denomination not found', null, 404);
        }

        return ResponseHelper::success('Denomination retrieved successfully', $denomination);
    }

    public function update(DenominationRequest $request, $currencyId, $id)
    {
        $denomination = $this->denominationService->updateDenomination($currencyId, $id, $request->validated());

        if (!$denomination) {
            return ResponseHelper::error('Denomination not found', null, 404);
        }

        return ResponseHelper::success('Denomination updated successfully', $denomination);
    }

    public function destroy($currencyId, $id)
    {
        $deleted = $this->denominationService->deleteDenomination($currencyId, $id);

        if (!$deleted) {
            return ResponseHelper::error('Denomination not found', null, 404);
        }

        return ResponseHelper::success('Denomination deleted successfully');
    }

    public function forceDestroy($currencyId, $id)
    {
        $deleted = $this->denominationService->forceDeleteDenomination($currencyId, $id);

        if (!$deleted) {
            return ResponseHelper::error('Denomination not found or could not be deleted', null, 404);
        }

        return ResponseHelper::success('Denomination permanently deleted successfully');
    }

    public function restore($currencyId, $id)
    {
        $restored = $this->denominationService->restoreDenomination($currencyId, $id);

        if (!$restored) {
            return ResponseHelper::error('Denomination not found or could not be restored', null, 404);
        }

        return ResponseHelper::success('Denomination restored successfully');
    }
}
