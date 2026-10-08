<?php

namespace App\Modules\AcademicYear\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Đặt các mốc lịch học vụ: {milestones: {loại: 'Y-m-d' | null}, reason}. Thứ tự do MilestoneService kiểm tra. */
class SaveMilestonesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'milestones' => ['required', 'array', 'min:1'],
            'milestones.*' => ['nullable', 'date_format:Y-m-d'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return ['milestones' => 'lịch học vụ', 'milestones.*' => 'ngày của mốc', 'reason' => 'lý do'];
    }
}
