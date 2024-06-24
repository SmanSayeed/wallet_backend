<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ResponseHelper; // Make sure to import your ResponseHelper class

class WalletDenominationRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'user_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    if ($value != auth()->id()) {
                        $fail('The '.$attribute.' must be the same as the authenticated user ID.');
                    }
                },
            ],
            'currency_id' => 'required|exists:currencies,id',
            'wallet_id' => 'required|exists:wallets,id',
            'denomination_id' => 'required|exists:denominations,id',
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
