<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RemoveWalletDenominationRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'user_id'=>'required|exists:users,id',
            'wallet_id' => 'required|exists:wallets,id',
            'denomination_pivot_id' => 'required',
        ];
    }
}
