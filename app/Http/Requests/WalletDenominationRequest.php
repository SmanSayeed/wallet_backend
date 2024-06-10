<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WalletDenominationRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'user_id'=>'required|exists:users,id',
            'currency_id'=>'required|exists:currencies,id',
            'wallet_id' => 'required|exists:wallets,id',
            'denomination_id' => 'required|exists:denominations,id',
        ];
    }
}
