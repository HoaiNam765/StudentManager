<?php

namespace App\Modules\Subject\Http\Requests;

use App\Modules\Subject\Enums\KnowledgeBlock;
use App\Modules\Subject\Enums\SubjectRequirement;
use App\Modules\Subject\Enums\TeachingLanguage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Tạo (POST) hoặc sửa (PUT) học phần. Quy tắc tín chỉ, mã, bộ môn do SubjectService kiểm tra (BR-SUB-01, 02, 05).
 */
class SubjectRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('code'))) {
            $this->merge(['code' => Str::upper(trim($this->input('code')))]);
        }
    }

    public function rules(): array
    {
        $creating = $this->isMethod('post');
        $required = $creating ? ['required'] : ['sometimes', 'required'];

        return [
            'code' => [...$required, 'string', 'max:20', 'regex:/^[A-Z0-9][A-Z0-9._\-]*$/'],
            'name' => [...$required, 'string', 'max:255'],
            'name_en' => ['sometimes', 'nullable', 'string', 'max:255'],
            'department_id' => [...$required, 'integer', 'exists:departments,id'],
            'description' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'teaching_language' => ['sometimes', Rule::enum(TeachingLanguage::class)],
            'credits' => [...$required, 'integer', 'min:0', 'max:255'],
            'theory_credits' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:255'],
            'practice_credits' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:255'],
            'theory_periods' => ['sometimes', 'integer', 'min:0', 'max:1000'],
            'practice_periods' => ['sometimes', 'integer', 'min:0', 'max:1000'],
            'self_study_hours' => ['sometimes', 'integer', 'min:0', 'max:2000'],
            'requirement' => ['sometimes', Rule::enum(SubjectRequirement::class)],
            'knowledge_block' => [...$required, Rule::enum(KnowledgeBlock::class)],
            'counts_toward_gpa' => ['sometimes', 'boolean'],
            'is_graduation_requirement' => ['sometimes', 'boolean'],
            'status' => [$creating ? 'prohibited' : 'sometimes', 'in:active,inactive'],
        ];
    }

    /** Bỏ các phần tín chỉ để trống (null) cho Service tự tính. */
    public function validated($key = null, $default = null): mixed
    {
        $data = parent::validated($key, $default);

        if ($key === null) {
            foreach (['theory_credits', 'practice_credits'] as $field) {
                if (array_key_exists($field, $data) && $data[$field] === null) {
                    unset($data[$field]);
                }
            }
        }

        return $data;
    }

    public function attributes(): array
    {
        return [
            'code' => 'mã học phần',
            'name' => 'tên học phần',
            'name_en' => 'tên tiếng Anh',
            'department_id' => 'bộ môn quản lý',
            'description' => 'mô tả',
            'teaching_language' => 'ngôn ngữ giảng dạy',
            'credits' => 'tổng tín chỉ',
            'theory_credits' => 'tín chỉ lý thuyết',
            'practice_credits' => 'tín chỉ thực hành',
            'theory_periods' => 'số tiết lý thuyết',
            'practice_periods' => 'số tiết thực hành',
            'self_study_hours' => 'giờ tự học',
            'requirement' => 'loại (bắt buộc / tự chọn)',
            'knowledge_block' => 'khối kiến thức',
            'counts_toward_gpa' => 'tính vào GPA',
            'is_graduation_requirement' => 'điều kiện tốt nghiệp',
            'status' => 'trạng thái',
        ];
    }

    public function messages(): array
    {
        return ['code.regex' => 'Mã học phần chỉ gồm chữ in hoa không dấu, số, dấu chấm, gạch ngang và gạch dưới (ví dụ INT1306, GDTC1).'];
    }
}
