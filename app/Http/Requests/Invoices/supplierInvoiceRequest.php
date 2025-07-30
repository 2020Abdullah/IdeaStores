<?php

namespace App\Http\Requests\Invoices;

use Illuminate\Foundation\Http\FormRequest;

class supplierInvoiceRequest extends FormRequest
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
        return [
            'supplier_id' => 'required',
            'invoice_date' => 'required',
            'invoice_type' => 'required',
            // 'additional_cost' => 'required|numeric',
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.category_id' => ['required', 'exists:categories,id'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:1'],
            'items.*.purchase_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'supplier_id.required' => 'يجب اختيار المورد !',
            'invoice_date.required' => 'يجب كتابة تاريخ الفاتورة !',
            'invoice_type.required' => 'يجب اختيار نوع الفاتورة !',
            // 'additional_cost.required' => 'يجب إضافة تكاليف الفاتورة !',
            // 'additional_cost.numeric' => 'يجب أن تكون التكاليف رقماً !',
            'items.required' => 'يجب إضافة أصناف إلى الفاتورة.',
            'items.min' => 'يجب أن تحتوي الفاتورة على صنف واحد على الأقل.',
            // category_id
            'items.*.category_id.required' => 'يجب اختيار التصنيف لكل صنف.',
            'items.*.category_id.exists' => 'التصنيف المختار غير موجود في النظام.',

            // product_id
            'items.*.product_id.required' => 'يجب اختيار المنتج لكل صنف.',
            'items.*.product_id.exists' => 'المنتج المختار غير موجود في النظام.',

            // quantity
            'items.*.quantity.required' => 'يرجى إدخال الكمية لكل صنف.',
            'items.*.quantity.numeric' => 'الكمية يجب أن تكون رقمًا.',
            'items.*.quantity.min' => 'يجب أن تكون الكمية على الأقل 1.',

            // purchase_price
            'items.*.purchase_price.required' => 'يجب إدخال سعر الشراء لكل صنف.',
            'items.*.purchase_price.numeric' => 'سعر الشراء يجب أن يكون رقمًا.',
            'items.*.purchase_price.min' => 'سعر الشراء لا يمكن أن يكون سالبًا.',

            // notes
            'items.*.notes.string' => 'الملاحظات يجب أن تكون نصًا.',
        ];
    }
}
