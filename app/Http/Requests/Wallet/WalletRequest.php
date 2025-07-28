<?php

namespace App\Http\Requests\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class WalletRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'method' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'يجب إدخال اسم المحفظة !',
            'method.required' => 'يجب إدخال نوع المحفظة !',
        ];
    }
}
