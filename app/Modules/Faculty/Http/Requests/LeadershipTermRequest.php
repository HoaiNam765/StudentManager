<?php

namespace App\Modules\Faculty\Http\Requests;

use App\Modules\Faculty\Enums\LeadershipPosition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Giao chức vụ lãnh đạo đơn vị theo nhiệm kỳ (FR-FAC-006). */
class LeadershipTermRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'unit_type' => ['required', 'in:faculty,department'],
            'unit_id' => ['required', 'integer'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'position' => ['required', Rule::enum(LeadershipPosition::class)],
            'starts_on' => ['required', 'date_format:Y-m-d'],
            'ends_on' => ['nullable', 'date_format:Y-m-d'],
            'note' => ['nullable', 'string', 'max:500'],
            'replace_current' => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'unit_type' => 'loại đơn vị',
            'unit_id' => 'đơn vị',
            'user_id' => 'người giữ chức vụ',
            'position' => 'chức vụ',
            'starts_on' => 'ngày bắt đầu',
            'ends_on' => 'ngày kết thúc',
            'note' => 'ghi chú',
            'replace_current' => 'thay thế người đang giữ chức vụ',
        ];
    }
}
