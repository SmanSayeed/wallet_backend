<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'denomination_ids' => 'required|array',
            'denomination_ids.*' => 'exists:wallet_denomination,id',
        ];
    }
}
