<?php

namespace App\Http\Requests\Units;

use Illuminate\Foundation\Http\FormRequest;

class UnitsRequest extends FormRequest
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
            'name' => [
                'required'
            ],
            'symbol' => [
                'required'
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'يجب إدخال اسم الوحدة',
            'symbol.required' => 'يجب إدخال رمز الوحدة',
        ];
    }
}
