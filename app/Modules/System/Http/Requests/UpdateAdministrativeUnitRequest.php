<?php

namespace App\Modules\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Mã, mô hình, cấp và đơn vị cấp trên không đổi sau khi tạo; sáp nhập thì tạo đơn vị mới và trỏ đơn vị cũ tới. */
class UpdateAdministrativeUnitRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => ['prohibited'],
            'scheme' => ['prohibited'],
            'level' => ['prohibited'],
            'parent_id' => ['prohibited'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'unit_type' => ['sometimes', 'required', 'string', 'max:50'],
            'successor_id' => ['sometimes', 'nullable', 'integer'],
            'status' => ['sometimes', 'required', 'in:active,inactive'],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'tên', 'unit_type' => 'loại đơn vị', 'successor_id' => 'đơn vị thay thế', 'status' => 'trạng thái'];
    }

    public function messages(): array
    {
        $hint = 'Khi đơn vị bị sáp nhập, đổi mã hoặc chuyển cấp trên, hãy tạo đơn vị mới, ngừng đơn vị cũ và trỏ đơn vị cũ tới đơn vị mới.';

        return [
            'code.prohibited' => 'Không đổi được mã đơn vị hành chính. '.$hint,
            'scheme.prohibited' => 'Không đổi được mô hình của đơn vị hành chính. '.$hint,
            'level.prohibited' => 'Không đổi được cấp của đơn vị hành chính. '.$hint,
            'parent_id.prohibited' => 'Không đổi được đơn vị cấp trên. '.$hint,
        ];
    }
}
