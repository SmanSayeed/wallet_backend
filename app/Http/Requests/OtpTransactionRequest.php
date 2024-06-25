<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ResponseHelper; // Make sure to import your ResponseHelper class

class OtpTransactionRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'otp' => 'required',
            'transaction_id' => 'required|exists:transactions,id',
            'wallet_denomination_pivot_ids' => 'required|array',
            'wallet_denomination_pivot_ids.*' => 'exists:wallet_denominations,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        throw new HttpResponseException(
            ResponseHelper::error('Validation failed', $errors, 422)
        );
    }
}
