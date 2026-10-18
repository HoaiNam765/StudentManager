<?php

namespace App\Modules\Auth\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Một lần đăng nhập thành công hoặc thất bại (FR-AUTH-016). Chỉ ghi thêm, không kế thừa StandardModel
 * (không cần người tạo/sửa, xóa mềm hay tự ghi nhật ký kiểm toán cho chính dòng lịch sử).
 */
class LoginHistory extends Model
{
    public const UPDATED_AT = null;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'successful' => 'boolean',
            'unusual' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
