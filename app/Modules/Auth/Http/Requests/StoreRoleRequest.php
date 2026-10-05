<?php

namespace App\Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreRoleRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('code'))) {
            $this->merge(['code' => Str::upper(trim($this->input('code')))]);
        }
    }

    public function rules(): array
    {
        return [
            // unique trên cả vai trò đã xóa mềm: mã không tái sử dụng
            'code' => ['required', 'string', 'max:20', 'regex:/^[A-Z][A-Z0-9_]*$/', 'unique:roles,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return ['code' => 'mã vai trò', 'name' => 'tên vai trò', 'description' => 'mô tả'];
    }

    public function messages(): array
    {
        return ['code.regex' => 'Mã vai trò chỉ gồm chữ in hoa không dấu, số và dấu gạch dưới, bắt đầu bằng chữ.'];
    }
}
