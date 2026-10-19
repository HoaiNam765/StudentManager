<?php

namespace App\Modules\Timetable\Listeners;

use App\Modules\SchoolClass\Events\CourseSectionCancelled;
use App\Modules\Timetable\Enums\SessionStatus;
use App\Modules\Timetable\Models\ClassSession;

/** Lớp bị hủy → các buổi học chưa diễn ra (Dự kiến, Dạy bù) chuyển Hủy, giữ lại để tra cứu. */
class CancelSessionsOfSection
{
    public function handle(CourseSectionCancelled $event): void
    {
        ClassSession::query()
            ->where('course_section_id', $event->section->id)
            ->whereIn('status', [SessionStatus::Planned->value, SessionStatus::Makeup->value])
            ->where('date', '>=', now(config('studentmanager.display_timezone'))->toDateString())
            ->get()
            ->each(fn (ClassSession $s) => $s->update(['status' => SessionStatus::Cancelled, 'off_reason' => "Lớp bị hủy: {$event->reason}"]));
    }
}
