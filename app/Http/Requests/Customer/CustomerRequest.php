<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
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
                Rule::unique('customers')->ignore($this->id)
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'يجب كتابة اسم العميل !',
            'name.unique' => 'هذا العميل مسجل بالفعل لدينا !',
        ];
    }
}
