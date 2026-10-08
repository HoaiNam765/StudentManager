<?php

namespace App\Modules\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLookupValueRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('code'))) {
            $this->merge(['code' => trim($this->input('code'))]);
        }
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:30', 'regex:/^[A-Za-z0-9_.\-]+$/'],
            'name' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return ['code' => 'mã', 'name' => 'tên', 'sort_order' => 'thứ tự'];
    }

    public function messages(): array
    {
        return ['code.regex' => 'Mã chỉ gồm chữ không dấu, số, dấu chấm, gạch ngang và gạch dưới.'];
    }
}
