<?php

namespace App\Modules\Auth\Http\Requests;

use App\Modules\Auth\Enums\ProfileType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Tạo (POST) hoặc sửa (PUT) tài khoản. Tên đăng nhập và loại hồ sơ cố định sau khi tạo (BR-AUTH-01);
 * trạng thái đổi bằng các thao tác khóa, mở khóa, ngừng, kích hoạt.
 */
class UserRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        foreach (['username', 'email'] as $field) {
            if (is_string($this->input($field))) {
                $this->merge([$field => trim($this->input($field))]);
            }
        }
    }

    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'username' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9._\-]+$/'],
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'profile_type' => ['required', Rule::enum(ProfileType::class)],
                'profile_id' => ['nullable', 'integer', 'min:1'],
                'roles' => ['sometimes', 'array'],
                'roles.*' => ['string', 'max:20'],
            ];
        }

        return [
            'username' => ['prohibited'],
            'profile_type' => ['prohibited'],
            'profile_id' => ['prohibited'],
            'status' => ['prohibited'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'username' => 'tên đăng nhập',
            'name' => 'họ và tên',
            'email' => 'email',
            'profile_type' => 'loại hồ sơ',
            'profile_id' => 'hồ sơ',
            'roles' => 'vai trò',
            'roles.*' => 'vai trò',
        ];
    }

    public function messages(): array
    {
        return [
            'username.regex' => 'Tên đăng nhập chỉ gồm chữ không dấu, số, dấu chấm, gạch ngang, gạch dưới (ví dụ MSSV hoặc mã cán bộ).',
            'username.prohibited' => 'Không đổi được tên đăng nhập; tên đăng nhập là duy nhất và không tái sử dụng (BR-AUTH-01).',
            'profile_type.prohibited' => 'Không đổi được loại hồ sơ của tài khoản.',
            'profile_id.prohibited' => 'Hồ sơ được gắn tự động khi STU/TCH tạo hồ sơ.',
            'status.prohibited' => 'Dùng thao tác khóa, mở khóa, ngừng hoặc kích hoạt để đổi trạng thái.',
        ];
    }
}
