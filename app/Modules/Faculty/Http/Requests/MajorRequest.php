<?php

namespace App\Modules\Faculty\Http\Requests;

use App\Modules\Faculty\Enums\EducationLevel;
use Illuminate\Validation\Rule;

/** Ngành (FR-FAC-003): khoa quản lý, trình độ, tổng tín chỉ chuẩn, thời gian đào tạo chuẩn (BR-FAC-07). */
class MajorRequest extends UnitRequest
{
    public function rules(): array
    {
        return $this->commonRules() + [
            'faculty_id' => [$this->creating() ? 'required' : 'prohibited', 'integer', 'exists:faculties,id'],
            'education_level' => ['sometimes', Rule::enum(EducationLevel::class)],
            'total_credits' => [...$this->required(), 'integer', 'between:1,400'],
            'standard_terms' => [...$this->required(), 'integer', 'between:1,20'],
        ];
    }
}
