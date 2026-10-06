<?php

namespace App\Modules\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation khi người dùng xác nhận lưu lô import (bước 4).
 */
class SaveImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        $batch = $this->route('batch');

        return $this->user()->can('save', $batch);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'mode' => ['required', Rule::in(['all_valid', 'all'])],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'mode.required' => 'Vui lòng chọn chế độ lưu.',
            'mode.in' => "Chế độ lưu không hợp lệ. Chọn 'all_valid' (chỉ lưu dòng hợp lệ) hoặc 'all' (lưu toàn bộ).",
        ];
    }
}
