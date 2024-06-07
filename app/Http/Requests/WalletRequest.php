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
            'currency_id' => 'required|exists:currencies,id',
            'user_id'=> 'required|exists:users,id',
            'name'=> 'required|string|max:255',
            'slug'=> 'required|string|max:255',
        ];
    }
}
