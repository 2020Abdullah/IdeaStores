<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
                Rule::unique('categories', 'name')->ignore($this->id),
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'يجب إدخال اسم التصنيف !',
            'name.unique' => 'هذا التصنيف موجود بالفعل، يُرجى اختيار اسم مختلف.',
        ];
    }
}
