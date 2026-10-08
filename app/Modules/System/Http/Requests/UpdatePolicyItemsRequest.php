<?php

namespace App\Modules\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Giá trị từng tham số do PolicySetService kiểm tra theo PolicyDefinitions (lỗi trả về ở `items.<khóa>`). */
class UpdatePolicyItemsRequest extends FormRequest
{
    public function rules(): array
    {
        return ['items' => ['required', 'array', 'min:1']];
    }

    public function attributes(): array
    {
        return ['items' => 'danh sách tham số'];
    }
}
