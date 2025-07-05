<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CateGoryRequest extends FormRequest
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
            'name_cate'    => 'required|string|min:1|max:255|unique:categories,name_cate',
            'description'  => 'nullable|string',
            'status'       => 'required|boolean',
        ];
    }

    /**
     * Get the validation messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name_cate.required' => 'Tên danh mục không được để trống.',
            'name_cate.string'   => 'Tên danh mục phải là một chuỗi ký tự.',
            'name_cate.min'      => 'Tên danh mục phải có ít nhất :min ký tự.',
            'name_cate.max'      => 'Tên danh mục không được vượt quá :max ký tự.',
            'name_cate.unique'   => 'Tên danh mục đã tồn tại.',


            'description.string'   => 'Mô tả phải là một chuỗi ký tự.',

            'status.required' => 'Trạng thái là bắt buộc.',
            'status.boolean'  => 'Trạng thái không hợp lệ.',
        ];
    }
}
