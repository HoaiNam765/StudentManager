<?php

namespace App\Modules\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StorePolicySetRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('code'))) {
            $this->merge(['code' => Str::upper(trim($this->input('code')))]);
        }
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:30', 'regex:/^[A-Z][A-Z0-9_\-]*$/'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'cohort_from' => ['nullable', 'integer', 'between:1990,2100'],
            'cohort_to' => ['nullable', 'integer', 'between:1990,2100'],
            'effective_from' => ['required', 'date'],
            'based_on_id' => ['nullable', 'integer', 'exists:policy_sets,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'mã bộ quy chế',
            'name' => 'tên bộ quy chế',
            'description' => 'mô tả',
            'cohort_from' => 'khóa bắt đầu áp dụng',
            'cohort_to' => 'khóa kết thúc áp dụng',
            'effective_from' => 'ngày hiệu lực',
            'based_on_id' => 'bộ quy chế gốc',
        ];
    }

    public function messages(): array
    {
        return ['code.regex' => 'Mã bộ quy chế chỉ gồm chữ in hoa không dấu, số, gạch ngang và gạch dưới (ví dụ QC-TT56).'];
    }
}
