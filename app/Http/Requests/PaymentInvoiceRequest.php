<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentInvoiceRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'warehouse_id'     => 'required|exists:warehouses,id',
            'wallet_id'        => 'required|exists:wallets,id',
            'amount'           => ['required', 'numeric', 'gt:0'],
        ];
    }

    public function messages()
    {
        return [
            'warehouse_id.required' => 'يجب اختيار الخزنة.',
            'wallet_id.required'    => 'يجب اختيار المحفظة.',
            'amount.required'       => 'يرجى إدخال المبلغ.',
            'amount.gt'             => 'يجب أن يكون المبلغ أكبر من صفر.',
        ];
    }

}
