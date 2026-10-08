<?php

namespace App\Modules\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Mã giá trị là khóa ổn định: không cho đổi (BR-SYS-09). */
class UpdateLookupValueRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => ['prohibited'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'sort_order' => ['sometimes', 'required', 'integer', 'min:0'],
            'status' => ['sometimes', 'required', 'in:active,inactive'],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'tên', 'sort_order' => 'thứ tự', 'status' => 'trạng thái'];
    }

    public function messages(): array
    {
        return ['code.prohibited' => 'Không đổi được mã; nếu cần mã mới, hãy tạo giá trị mới và ngừng sử dụng giá trị cũ.'];
    }
}
