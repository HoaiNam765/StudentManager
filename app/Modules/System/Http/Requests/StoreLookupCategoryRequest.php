<?php

namespace App\Modules\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreLookupCategoryRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:30', 'regex:/^[A-Z][A-Z0-9_]*$/'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return ['code' => 'mã danh mục', 'name' => 'tên danh mục', 'description' => 'mô tả'];
    }

    public function messages(): array
    {
        return ['code.regex' => 'Mã danh mục chỉ gồm chữ in hoa không dấu, số và dấu gạch dưới, bắt đầu bằng chữ (ví dụ CONTRACT_TYPE).'];
    }
}
