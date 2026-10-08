<?php

namespace App\Modules\System\Http\Requests;

use App\Modules\System\Enums\AdministrativeLevel;
use App\Modules\System\Enums\AdministrativeScheme;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdministrativeUnitRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'scheme' => ['required', Rule::enum(AdministrativeScheme::class)],
            'level' => ['required', Rule::enum(AdministrativeLevel::class)],
            'code' => ['required', 'string', 'regex:/^\d{2,10}$/'],
            'name' => ['required', 'string', 'max:255'],
            'unit_type' => ['required', 'string', 'max:50'],
            'parent_id' => ['nullable', 'integer'],
            'successor_id' => ['nullable', 'integer'],
        ];
    }

    public function attributes(): array
    {
        return [
            'scheme' => 'mô hình',
            'level' => 'cấp',
            'code' => 'mã',
            'name' => 'tên',
            'unit_type' => 'loại đơn vị',
            'parent_id' => 'đơn vị cấp trên',
            'successor_id' => 'đơn vị thay thế',
        ];
    }

    public function messages(): array
    {
        return ['code.regex' => 'Mã đơn vị hành chính gồm 2 đến 10 chữ số (giữ nguyên số 0 ở đầu).'];
    }
}
