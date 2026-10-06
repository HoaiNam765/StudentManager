<?php

namespace App\Modules\AcademicYear\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTermRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_year_id' => [
                'required',
                'integer',
                'exists:academic_years,id',
            ],

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'type' => [
                'required',
                Rule::in([
                    'main',
                    'summer',
                ]),
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'required',
                'date',
                'after:start_date',
            ],

            'weeks' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'academic_year_id.required' => 'Vui lòng chọn năm học.',

            'academic_year_id.exists' => 'Năm học không tồn tại.',

            'name.required' => 'Vui lòng nhập tên học kỳ.',

            'type.required' => 'Vui lòng chọn loại học kỳ.',

            'type.in' => 'Loại học kỳ không hợp lệ.',

            'start_date.required' => 'Vui lòng nhập ngày bắt đầu học kỳ.',

            'end_date.required' => 'Vui lòng nhập ngày kết thúc học kỳ.',

            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',

            'weeks.required' => 'Vui lòng nhập số tuần học.',

            'weeks.min' => 'Số tuần học phải lớn hơn 0.',
        ];
    }
}
