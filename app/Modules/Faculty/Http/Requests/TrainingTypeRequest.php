<?php

namespace App\Modules\Faculty\Http\Requests;

/** Hệ đào tạo (FR-FAC-005). Mã cố định sau khi tạo. */
class TrainingTypeRequest extends UnitRequest
{
    public function rules(): array
    {
        return [
            'code' => [$this->creating() ? 'required' : 'prohibited', 'string', 'max:20', self::CODE_RULE],
            'name' => [...$this->required(), 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:500'],
            'is_default' => ['sometimes', 'boolean'],
            'status' => [$this->creating() ? 'prohibited' : 'sometimes', 'in:active,inactive'],
        ];
    }
}
