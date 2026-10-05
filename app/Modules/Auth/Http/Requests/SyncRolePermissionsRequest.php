<?php

namespace App\Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Ma trận quyền mới của vai trò: { "permissions": { "STU.view": "ALL", "GRD.update": "SECTION" } }.
 * Gửi object rỗng để gỡ hết quyền. Tên quyền được kiểm tra ở RoleService.
 */
class SyncRolePermissionsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'permissions' => ['present', 'array'],
            'permissions.*' => ['required', 'string', 'in:ALL,FACULTY,SECTION,ADVISEE,OWN'],
        ];
    }

    public function attributes(): array
    {
        return ['permissions' => 'ma trận quyền', 'permissions.*' => 'phạm vi dữ liệu'];
    }
}
