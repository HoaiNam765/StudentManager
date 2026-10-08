<?php

namespace App\Modules\Faculty\Http\Requests;

/** Bộ môn (FR-FAC-002): thuộc đúng một khoa, không chuyển khoa khi sửa (BR-FAC-02). */
class DepartmentRequest extends UnitRequest
{
    public function rules(): array
    {
        return $this->commonRules() + [
            'faculty_id' => [$this->creating() ? 'required' : 'prohibited', 'integer', 'exists:faculties,id'],
            'description' => ['sometimes', 'nullable', 'string', 'max:5000'],
        ];
    }
}
