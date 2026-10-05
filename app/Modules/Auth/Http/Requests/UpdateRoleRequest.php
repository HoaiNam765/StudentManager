<?php

namespace App\Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Không cho đổi mã vai trò: mã được dùng để tham chiếu và tra nhật ký. */
class UpdateRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:500'],
            'status' => ['sometimes', 'required', 'in:active,inactive'],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'tên vai trò', 'description' => 'mô tả', 'status' => 'trạng thái'];
    }
}
