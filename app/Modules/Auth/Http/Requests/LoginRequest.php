<?php

namespace App\Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Trường của biểu mẫu đăng nhập (giao diện FE dùng đúng các tên này): login, password, remember. */
class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
            'remember' => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return ['login' => 'tên đăng nhập hoặc email', 'password' => 'mật khẩu'];
    }
}
