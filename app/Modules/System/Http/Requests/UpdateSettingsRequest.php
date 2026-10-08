<?php

namespace App\Modules\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Kiểm tra từng tham số do SettingService làm theo SettingDefinitions (lỗi trả về ở `values.<khóa>`). */
class UpdateSettingsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'values' => ['required', 'array', 'min:1'],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return ['values' => 'danh sách tham số', 'reason' => 'lý do'];
    }
}
