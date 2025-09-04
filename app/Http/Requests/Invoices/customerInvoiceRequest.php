<?php

namespace App\Http\Requests\Invoices;

use Illuminate\Foundation\Http\FormRequest;

class customerInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'customer_id' => 'required',
            'date' => 'required|date',
            'invoice_type' => 'required',
            'notes' => ['nullable', 'string'],
        ];

        if ($this->input('invoice_type') !== 'opening_balance') {
            $rules['items'] = ['required', 'array', 'min:1'];
            $rules['items.*.category_id'] = ['required', 'exists:categories,id'];
            $rules['items.*.quantity'] = ['required', 'numeric', 'min:1'];
            $rules['items.*.sale_price'] = [
                'required',
                'regex:/^\d+([.,]\d+)?$/', 
                'min:0'
            ];
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'customer_id.required' => 'يجب اختيار العميل !',
            'date.required' => 'يجب كتابة تاريخ الفاتورة !',
            'invoice_type.required' => 'يجب اختيار نوع الفاتورة !',
            'items.required' => 'يجب إضافة أصناف إلى الفاتورة.',
            'items.min' => 'يجب أن تحتوي الفاتورة على صنف واحد على الأقل.',
            // category_id
            'items.*.category_id.required' => 'يجب اختيار التصنيف لكل صنف.',
            'items.*.category_id.exists' => 'التصنيف المختار غير موجود في النظام.',

            // quantity
            'items.*.quantity.required' => 'يرجى إدخال الكمية لكل صنف.',
            'items.*.quantity.numeric' => 'الكمية يجب أن تكون رقمًا.',
            'items.*.quantity.min' => 'يجب أن تكون الكمية على الأقل 1.',

            // sale_price
            'items.*.sale_price.required' => 'يجب إدخال سعر البيع لكل صنف.',
            'items.*.sale_price.numeric' => 'سعر البيع يجب أن يكون رقمًا.',
            'items.*.sale_price.regex' => 'سعر البيع يجب أن يكون رقمًا ويمكن أن يحتوي على فاصلة عشرية.',
            'items.*.sale_price.min' => 'سعر البيع لا يمكن أن يكون سالبًا.',

            // notes
            'items.*.notes.string' => 'الملاحظات يجب أن تكون نصًا.',
        ];
    }
}
