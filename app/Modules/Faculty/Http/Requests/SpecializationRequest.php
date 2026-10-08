<?php

namespace App\Modules\Faculty\Http\Requests;

/** Chuyên ngành (FR-FAC-004): thuộc đúng một ngành, không chuyển ngành khi sửa (BR-FAC-02). */
class SpecializationRequest extends UnitRequest
{
    public function rules(): array
    {
        return $this->commonRules() + [
            'major_id' => [$this->creating() ? 'required' : 'prohibited', 'integer', 'exists:majors,id'],
            'description' => ['sometimes', 'nullable', 'string', 'max:5000'],
        ];
    }
}
