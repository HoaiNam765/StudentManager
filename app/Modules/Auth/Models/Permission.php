<?php

namespace App\Modules\Auth\Models;

use App\Modules\Auth\Enums\PermissionAction;
use Illuminate\Database\Eloquent\Model;

/**
 * Một quyền trong danh mục: module (mã trong BA, ví dụ STU) + hành động.
 * Phạm vi dữ liệu nằm ở quyền của từng vai trò (bảng role_permissions).
 */
class Permission extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['action' => PermissionAction::class];
    }

    /** Khóa dạng "STU.view". */
    public function key(): string
    {
        return $this->module.'.'.$this->action->value;
    }
}
