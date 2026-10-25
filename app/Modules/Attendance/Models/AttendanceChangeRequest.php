<?php

namespace App\Modules\Attendance\Models;

use App\Models\User;
use App\Modules\Attendance\Enums\AttendanceStatus;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Yêu cầu sửa điểm danh quá khung thời gian cho phép, chờ Phòng Đào tạo duyệt (FR-ATT-005). */
class AttendanceChangeRequest extends StandardModel
{
    public const PENDING = 'pending';

    public const APPROVED = 'approved';

    public const REJECTED = 'rejected';

    public const STATUS_LABELS = [self::PENDING => 'Chờ duyệt', self::APPROVED => 'Đã duyệt', self::REJECTED => 'Bị từ chối'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'from_status' => AttendanceStatus::class,
            'to_status' => AttendanceStatus::class,
            'minutes_late' => 'integer',
            'decided_at' => 'datetime',
        ];
    }

    public function record(): BelongsTo
    {
        return $this->belongsTo(AttendanceRecord::class, 'attendance_record_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
