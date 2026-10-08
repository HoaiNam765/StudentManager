<?php

namespace App\Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/** Đặt mật khẩu mới bằng mã trong liên kết; mật khẩu mới theo chính sách cấu hình (FR-AUTH-005). */
class ResetPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ];
    }

    public function attributes(): array
    {
        return ['token' => 'mã đặt lại', 'email' => 'email', 'password' => 'mật khẩu mới'];
    }
}
