<?php
namespace App\Http\Controllers\Api\V1\Wallets;

use App\Http\Controllers\Controller;
use App\Services\CurrencyService;
use App\Helpers\ResponseHelper;
use App\Http\Requests\CurrencyRequest;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function index(Request $request)
    {
        $currencies = $this->currencyService->getAllCurrencies();
        return ResponseHelper::success('Currencies retrieved successfully', $currencies);
    }

    public function store(CurrencyRequest $request)
    {
        $currency = $this->currencyService->createCurrency($request->validated());
        return ResponseHelper::success('Currency created successfully', $currency, 201);
    }

    public function show($id)
    {
        $currency = $this->currencyService->getCurrencyById($id);

        if (!$currency) {
            return ResponseHelper::error('Currency not found', null, 404);
        }

        return ResponseHelper::success('Currency retrieved successfully', $currency);
    }

    public function update(CurrencyRequest $request, $id)
    {
        $currency = $this->currencyService->updateCurrency($id, $request->validated());

        if (!$currency) {
            return ResponseHelper::error('Currency not found', null, 404);
        }

        return ResponseHelper::success('Currency updated successfully', $currency);
    }

    public function destroy($id)
    {
        $deleted = $this->currencyService->deleteCurrency($id);

        if (!$deleted) {
            return ResponseHelper::error('Currency not found', null, 404);
        }

        return ResponseHelper::success('Currency deleted successfully');
    }
}
