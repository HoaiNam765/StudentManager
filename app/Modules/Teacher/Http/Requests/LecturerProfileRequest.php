<?php

namespace App\Modules\Teacher\Http\Requests;

use App\Modules\Teacher\Enums\AcademicDegree;
use App\Modules\Teacher\Enums\AcademicTitle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Giảng viên tự cập nhật hồ sơ (BR-TCH-07): liên hệ sửa ngay; họ tên, chức danh, học vị gửi đề nghị chờ xác nhận.
 * Trường khác bị từ chối ở LecturerProfileService (không âm thầm bỏ qua).
 */
class LecturerProfileRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => Str::lower(trim($this->input('email')))]);
        }
    }

    public function rules(): array
    {
        return [
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'full_name' => ['sometimes', 'required', 'string', 'max:255'],
            'academic_title' => ['sometimes', Rule::enum(AcademicTitle::class)],
            'academic_degree' => ['sometimes', 'nullable', Rule::enum(AcademicDegree::class)],
            'reason' => ['sometimes', 'nullable', 'string', 'max:500'],
        ];
    }

    /** Giữ cả các trường không có trong rules() để Service báo rõ trường nào không được tự sửa. */
    public function payload(): array
    {
        $validated = $this->validated();
        unset($validated['reason']);

        return $validated + array_diff_key($this->except(['reason', '_token', '_method']), $this->rules());
    }

    public function attributes(): array
    {
        return [
            'phone' => 'điện thoại',
            'email' => 'email',
            'full_name' => 'họ và tên',
            'academic_title' => 'chức danh',
            'academic_degree' => 'học vị',
            'reason' => 'lý do',
        ];
    }
}
