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
            'wallet_id' => 'required|exists:wallets,id',
            'denomination_id' => 'required|exists:denominations,id',
        ];
    }
}
