<?php

namespace App\Modules\AcademicYear\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAcademicYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:50',
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
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên năm học.',

            'name.max' => 'Tên năm học không được vượt quá 50 ký tự.',

            'start_date.required' => 'Vui lòng nhập ngày bắt đầu năm học.',

            'start_date.date' => 'Ngày bắt đầu năm học không hợp lệ.',

            'end_date.required' => 'Vui lòng nhập ngày kết thúc năm học.',

            'end_date.date' => 'Ngày kết thúc năm học không hợp lệ.',

            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
        ];
    }
}
