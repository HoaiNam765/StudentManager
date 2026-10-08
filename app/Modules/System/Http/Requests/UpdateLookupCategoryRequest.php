<?php

namespace App\Modules\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Mã danh mục là khóa ổn định: không cho đổi (BR-SYS-09). */
class UpdateLookupCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => ['prohibited'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:500'],
            'status' => ['sometimes', 'required', 'in:active,inactive'],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'tên danh mục', 'description' => 'mô tả', 'status' => 'trạng thái'];
    }

    public function messages(): array
    {
        return ['code.prohibited' => 'Không đổi được mã danh mục; nếu cần mã mới, hãy tạo danh mục mới và ngừng danh mục cũ.'];
    }
}
