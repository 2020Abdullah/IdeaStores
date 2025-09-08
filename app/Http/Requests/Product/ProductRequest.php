<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
            'final_category_id' => [
                'required'
            ],
            'name' => [
                'required',
                'string',
                Rule::unique('products')
                ->ignore($this->id)
            ],
        ];
    }

    public function messages()
    {
        return [
            'final_category_id.required' => 'يجب اختيار التصنيف !',
            'name.required' => 'يجب كتابة اسم المنتج !',
            'name.unique' => 'هذا المنتج مسجل بالفعل يرجى كتابة اسم آخر !',
        ];
    }
}
