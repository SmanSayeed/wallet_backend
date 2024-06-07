<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CurrencyRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'code' => 'required|string|max:3|unique:currencies,code',
            'name' => 'required|string|max:255',
        ];
    }
}
