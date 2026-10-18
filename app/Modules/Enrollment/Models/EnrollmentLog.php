<?php

namespace App\Modules\Enrollment\Models;

use Illuminate\Database\Eloquent\Model;

/** Nhật ký mỗi lần thử đăng ký / hủy / rút / đổi lớp (FR-ENR-017). Chỉ ghi thêm. */
class EnrollmentLog extends Model
{
    public const UPDATED_AT = null;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'success' => 'boolean',
            'reasons' => 'array',
        ];
    }
}
