<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
            'category_id' => 'required',
            'name' => 'required',
            'info' => 'required',
            'imagePath' => 'required|image|mimes:png,jpg,jpeg|max:5048',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'اسم المشروع مطلوب !',
            'info.required' => 'وصف المشروع مطلوب !',
            'category_id.required' => 'تصنيف المشروع مطلوب !',
            'imagePath.required' => 'صورة المشروع مطلوبة !',
            'imagePath.image' => 'يجب أن يكون الملف صورة !',
            'imagePath.mimes' => 'يجب إدخال صور من نوع png أو jpg !',
            'imagePath.max' => 'يجب أن يكون الملف أقل من 5 ميجا !',
        ];
    }
}
