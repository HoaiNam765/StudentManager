<?php

namespace App\Modules\AcademicYear\Http\Requests;

use App\Modules\AcademicYear\Models\HolidayType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Thêm (POST) hoặc sửa (PUT) ngày nghỉ (FR-ACY-005). */
class HolidayRequest extends FormRequest
{
    public function rules(): array
    {
        $required = $this->isMethod('post') ? ['required'] : ['sometimes', 'required'];

        return [
            'name' => [...$required, 'string', 'max:255'],
            'type' => [...$required, Rule::enum(HolidayType::class)],
            'starts_on' => [...$required, 'date_format:Y-m-d'],
            'ends_on' => [...$required, 'date_format:Y-m-d'],
            'term_id' => ['sometimes', 'nullable', 'integer', 'exists:terms,id'],
            'note' => ['sometimes', 'nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'tên ngày nghỉ',
            'type' => 'loại ngày nghỉ',
            'starts_on' => 'ngày bắt đầu',
            'ends_on' => 'ngày kết thúc',
            'term_id' => 'học kỳ',
            'note' => 'ghi chú',
        ];
    }
}
