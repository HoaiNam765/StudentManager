<?php

namespace App\Modules\Teacher\Models;

use App\Models\User;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Giảng viên đề nghị đổi họ tên, chức danh, học vị (BR-TCH-07): chờ cán bộ có thẩm quyền (khác người đề nghị) xác nhận
 * rồi mới áp dụng vào hồ sơ. `changes` dạng {trường: {old, new}}.
 */
class LecturerChangeRequest extends StandardModel
{
    public const PENDING = 'pending';

    public const APPROVED = 'approved';

    public const REJECTED = 'rejected';

    public const CANCELLED = 'cancelled';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['changes' => 'array', 'decided_at' => 'datetime'];
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function isPending(): bool
    {
        return $this->status === self::PENDING;
    }
}
