<?php

namespace App\Modules\Attendance\Events;

use App\Modules\Attendance\Models\AttendanceSummary;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Sinh viên vừa chạm ngưỡng cảnh báo vắng (`warning`, EV-ATT-01) hoặc vượt ngưỡng cấm thi (`banned`, EV-ATT-02).
 * Phát một lần cho mỗi mức (theo `warned_at` / `banned_at`); NOT gửi thông báo, EXM đọc cờ `banned` khi lập danh sách thi.
 */
class AttendanceThresholdReached
{
    use Dispatchable;

    public const WARNING = 'warning';

    public const BANNED = 'banned';

    public function __construct(
        public readonly AttendanceSummary $summary,
        public readonly string $level,
    ) {}
}
