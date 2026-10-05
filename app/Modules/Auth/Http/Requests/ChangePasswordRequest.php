<?php

namespace App\Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/** Trường của biểu mẫu đổi mật khẩu: current_password, password, password_confirmation. */
class ChangePasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', 'different:current_password', Password::defaults()],
        ];
    }

    public function attributes(): array
    {
        return ['current_password' => 'mật khẩu hiện tại', 'password' => 'mật khẩu mới'];
    }
}
