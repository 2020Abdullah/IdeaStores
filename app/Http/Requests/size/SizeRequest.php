<?php

namespace App\Http\Requests\size;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SizeRequest extends FormRequest
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
            'width' => [
                'required',
                'string',
                Rule::unique('sizes')->ignore($this->id)
            ],
        ];
    }

    public function messages()
    {
        return [
            'width.required' => 'يجب كتابة المقاس !',
            'width.unique' => 'هذا المقاس مسجل بالفعل !',
        ];
    }
}
