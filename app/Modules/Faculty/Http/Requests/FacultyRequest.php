<?php

namespace App\Modules\Faculty\Http\Requests;

/** Khoa (FR-FAC-001): mã, tên (vi/en), ngày thành lập, liên hệ, mô tả, trạng thái. */
class FacultyRequest extends UnitRequest
{
    public function rules(): array
    {
        return $this->commonRules() + [
            'founded_on' => ['sometimes', 'nullable', 'date_format:Y-m-d'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'description' => ['sometimes', 'nullable', 'string', 'max:5000'],
        ];
    }
}
