<?php

namespace App\Http\Requests\Wallet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            Rule::unique('wallets')->ignore($this->id)
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'يجب إدخال اسم المحفظة !',
            'name.unique' => 'هذه المحفظة مسجلة بالفعل !',
        ];
    }
}
