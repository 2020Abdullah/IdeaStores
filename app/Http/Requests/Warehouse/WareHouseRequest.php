<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

class WareHouseRequest extends FormRequest
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
                'required',
                'string',
                'regex:/^[\pL\s\-]+$/u',
                'max:100'
            ]
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'حقل اسم الخزنة مطلوب !',
            'name.string' => 'يجب أن يكون حقل الإسم ليس رقم !',
            'name.regex'  => 'برجاء إدخال اسم صحيح !',
            'name.max'    => 'يجب أن يكون الإسم أقل من أو يساوى 100 حرف !'
        ];
    }
}
