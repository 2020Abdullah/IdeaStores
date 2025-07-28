<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuoteRequestForm extends FormRequest
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
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'digits_between:6,20'],
            'service_id' => ['required', 'string', 'max:100'],
            'message' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'يرجى إدخال الاسم الكامل',
            'email.required' => 'يرجى إدخال البريد الإلكتروني',
            'email.email' => 'صيغة البريد غير صحيحة',
            'phone.required' => 'يرجى إدخال رقم الهاتف',
            'phone.digits_between' => 'يجب إدخال رقم هاتف صحيح',
            'service_id.required' => 'يرجى اختيار نوع الخدمة',
        ];
    }
}
