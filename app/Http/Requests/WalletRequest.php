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
            'name'=> 'required',
            'slug'=>'required|string|max:255|unique:wallets,slug',
            'user_id'=>'required|exists:users,id',
            'balance'=>'numeric',
            'currency_id' => 'required|exists:currencies,id',
        ];
    }
}
