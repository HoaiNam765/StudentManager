<?php

namespace App\Modules\Faculty\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

/**
 * Nền cho biểu mẫu khoa, bộ môn, ngành, chuyên ngành, hệ đào tạo: chuẩn hóa mã, tên trường tiếng Việt.
 * Tạo bằng POST, sửa bằng PUT; lớp con khai báo `rules()` dùng `$this->creating()`.
 */
abstract class UnitRequest extends FormRequest
{
    protected const CODE_RULE = 'regex:/^[A-Z0-9][A-Z0-9._\-]*$/';

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('code'))) {
            $this->merge(['code' => Str::upper(trim($this->input('code')))]);
        }
    }

    protected function creating(): bool
    {
        return $this->isMethod('post');
    }

    /** @return list<string> */
    protected function required(): array
    {
        return $this->creating() ? ['required'] : ['sometimes', 'required'];
    }

    /** Quy tắc chung: mã, tên (vi/en), trạng thái (chỉ khi sửa). */
    protected function commonRules(): array
    {
        return [
            'code' => [...$this->required(), 'string', 'max:20', self::CODE_RULE],
            'name' => [...$this->required(), 'string', 'max:255'],
            'name_en' => ['sometimes', 'nullable', 'string', 'max:255'],
            'status' => [$this->creating() ? 'prohibited' : 'sometimes', 'in:active,inactive'],
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'mã',
            'name' => 'tên',
            'name_en' => 'tên tiếng Anh',
            'faculty_id' => 'khoa',
            'major_id' => 'ngành',
            'founded_on' => 'ngày thành lập',
            'email' => 'email',
            'phone' => 'điện thoại',
            'description' => 'mô tả',
            'education_level' => 'trình độ',
            'total_credits' => 'tổng tín chỉ chuẩn',
            'standard_terms' => 'thời gian đào tạo chuẩn (số học kỳ)',
            'is_default' => 'hệ mặc định',
            'status' => 'trạng thái',
        ];
    }

    public function messages(): array
    {
        return [
            'code.regex' => 'Mã chỉ gồm chữ in hoa không dấu, số, dấu chấm, gạch ngang và gạch dưới (ví dụ CNTT, BM-KTPM, 7480201).',
            'faculty_id.prohibited' => 'Không chuyển được sang khoa khác; việc chuyển, sáp nhập đơn vị làm theo quy trình riêng (FR-FAC-009).',
            'major_id.prohibited' => 'Không chuyển được chuyên ngành sang ngành khác; hãy tạo chuyên ngành mới và ngừng chuyên ngành cũ.',
        ];
    }
}
