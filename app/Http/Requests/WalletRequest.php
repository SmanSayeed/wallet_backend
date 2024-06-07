<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WalletRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'currency_code' => 'required|exists:currencies,currency_code',
        ];
    }
}
