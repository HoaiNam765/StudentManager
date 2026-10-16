<?php

namespace App\Modules\Teacher\Http\Requests;

use App\Modules\Teacher\Enums\AcademicDegree;
use App\Modules\Teacher\Enums\AcademicTitle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Tạo (POST) hoặc sửa (PUT) hồ sơ giảng viên. Quy tắc mã, đơn vị chính, tài khoản do LecturerService kiểm tra.
 * Đổi tình trạng công tác dùng route riêng (`/status`).
 */
class LecturerRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        foreach (['code', 'national_id'] as $field) {
            if (is_string($this->input($field))) {
                $this->merge([$field => Str::upper(trim($this->input($field)))]);
            }
        }

        if (is_string($this->input('email'))) {
            $this->merge(['email' => Str::lower(trim($this->input('email')))]);
        }
    }

    public function rules(): array
    {
        $creating = $this->isMethod('post');
        $required = $creating ? ['required'] : ['sometimes', 'required'];

        return [
            'code' => [...$required, 'string', 'max:20', 'regex:/^[A-Z0-9][A-Z0-9._\-]*$/'],
            'full_name' => [...$required, 'string', 'max:255'],
            'date_of_birth' => ['sometimes', 'nullable', 'date_format:Y-m-d', 'before:today'],
            'gender_id' => ['sometimes', 'nullable', 'integer', 'exists:lookup_values,id'],
            'national_id' => ['sometimes', 'nullable', 'string', 'regex:/^[0-9]{12}$/'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'department_id' => [...$required, 'integer', 'exists:departments,id'],
            'academic_title' => ['sometimes', Rule::enum(AcademicTitle::class)],
            'academic_degree' => ['sometimes', 'nullable', Rule::enum(AcademicDegree::class)],
            'expertise' => ['sometimes', 'nullable', 'string', 'max:500'],
            'hired_on' => ['sometimes', 'nullable', 'date_format:Y-m-d'],
            'account' => [$creating ? 'sometimes' : 'prohibited', 'in:new,link,none'],
            'user_id' => [$creating ? 'required_if:account,link' : 'prohibited', 'nullable', 'integer', 'exists:users,id'],
            'status' => ['prohibited'],
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'mã giảng viên',
            'full_name' => 'họ và tên',
            'date_of_birth' => 'ngày sinh',
            'gender_id' => 'giới tính',
            'national_id' => 'số CCCD',
            'email' => 'email',
            'phone' => 'điện thoại',
            'department_id' => 'đơn vị chính (bộ môn)',
            'academic_title' => 'chức danh',
            'academic_degree' => 'học vị',
            'expertise' => 'chuyên môn',
            'hired_on' => 'ngày vào làm',
            'account' => 'tài khoản',
            'user_id' => 'tài khoản có sẵn',
        ];
    }

    public function messages(): array
    {
        return [
            'code.regex' => 'Mã giảng viên chỉ gồm chữ in hoa không dấu, số, dấu chấm, gạch ngang và gạch dưới (ví dụ GV0001).',
            'national_id.regex' => 'Số CCCD gồm đúng 12 chữ số.',
            'status.prohibited' => 'Đổi tình trạng công tác (nghỉ việc, nghỉ hưu) dùng thao tác riêng, có lý do (BR-TCH-02).',
        ];
    }
}
