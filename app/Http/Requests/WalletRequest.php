<?php
namespace App\Http\Requests;

use App\Helpers\ResponseHelper;
use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class WalletRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'name'=> 'required',
            'slug'=>'required|string|max:255|unique:wallets,slug',
            'user_id'=>[
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    if ($value != auth()->id()) {
                        $fail('The '.$attribute.' must be the same as the authenticated user ID.');
                    }
                },
            ],
            'balance'=>'numeric',
            'currency_id' => 'required|exists:currencies,id',

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
