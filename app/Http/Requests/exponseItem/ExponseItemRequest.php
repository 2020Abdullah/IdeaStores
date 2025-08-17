<?php

namespace App\Http\Requests\exponseItem;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExponseItemRequest extends FormRequest
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
                Rule::unique('exponse_items')->ignore($this->id)
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'يجب كتابة بند المصروف !',
            'name.unique' => 'هذا البند مسجل من قبل !',
        ];
    }
}
