<?php

namespace App\Modules\Timetable\Services;

use App\Models\User;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Timetable\Enums\TimetableStatus;
use App\Modules\Timetable\Events\TimetablePublished;
use App\Modules\Timetable\Models\ClassSession;
use App\Modules\Timetable\Models\TermTimetable;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;

/**
 * Nháp → công bố → khóa thời khóa biểu theo học kỳ (FR-TTB-006; BR-TTB-05).
 *
 * - Công bố: phát EV-TTB-01 (`TimetablePublished`). Từ đó sinh viên, giảng viên mới xem được lịch của học kỳ.
 * - Sau công bố, đổi lịch phải có lý do; buổi bị ảnh hưởng sớm nhất phải còn ít nhất `change_notice_hours` giờ (mặc định
 *   24), ngắn hơn thì chỉ được khi đánh dấu khẩn cấp (ghi nhận trong nhật ký và sự kiện EV-TTB-02).
 * - Khóa: không đổi lịch nữa (cuối học kỳ).
 */
class TimetablePublication extends BaseService
{
    public function status(Term $term): TimetableStatus
    {
        return TermTimetable::query()->where('term_id', $term->id)->value('status') ?? TimetableStatus::Draft;
    }

    public function isVisible(Term $term): bool
    {
        return $this->status($term) !== TimetableStatus::Draft;
    }

    public function publish(Term $term, ?User $actor = null): TermTimetable
    {
        $record = TermTimetable::query()->firstOrNew(['term_id' => $term->id]);

        if ($record->exists && $record->status !== TimetableStatus::Draft) {
            $this->fail("Thời khóa biểu {$term->name} đang ở trạng thái {$record->status->label()}.", 'Chỉ công bố thời khóa biểu đang ở trạng thái nháp.');
        }

        $sections = CourseSection::query()->where('term_id', $term->id)->active()->pluck('id');

        if ($sections->isEmpty() || ! ClassSession::query()->whereIn('course_section_id', $sections)->exists()) {
            $this->fail("{$term->name} chưa có lớp học phần nào được xếp lịch.", 'Xếp lịch cho các lớp (FR-TTB-002) trước khi công bố.');
        }

        $record->fill(['status' => TimetableStatus::Published, 'published_at' => now(), 'published_by' => $actor?->id])->save();
        TimetablePublished::dispatch($term);

        return $record;
    }

    public function lock(Term $term): TermTimetable
    {
        $record = TermTimetable::query()->where('term_id', $term->id)->first();

        if ($record === null || $record->status !== TimetableStatus::Published) {
            $this->fail("Thời khóa biểu {$term->name} chưa công bố.", 'Công bố trước rồi mới khóa.');
        }

        $record->update(['status' => TimetableStatus::Locked, 'locked_at' => now()]);

        return $record;
    }

    /**
     * Kiểm tra được đổi lịch của lớp không (BR-TTB-05). Trả lý do đã chuẩn hóa (null khi thời khóa biểu còn nháp).
     *
     * @param  list<CarbonImmutable>  $affectedDates  ngày các buổi bị ảnh hưởng (lịch cũ và lịch mới)
     */
    public function assertChangeAllowed(CourseSection $section, ?string $reason, bool $urgent, array $affectedDates): ?string
    {
        $section->loadMissing('term');
        $status = $this->status($section->term);

        if ($status === TimetableStatus::Locked) {
            $this->fail("Thời khóa biểu {$section->term->name} đã khóa.", 'Không đổi lịch sau khi khóa; liên hệ quản trị nếu cần mở khóa.');
        }

        if ($status === TimetableStatus::Draft) {
            return null;
        }

        $reason = trim((string) $reason);

        if ($reason === '') {
            $this->fail('Thời khóa biểu đã công bố nên phải ghi lý do thay đổi.', 'Nhập lý do (BR-TTB-05); sinh viên và giảng viên của lớp sẽ được thông báo.');
        }

        $hours = (int) config('studentmanager.timetable.change_notice_hours', 24);
        $deadline = CarbonImmutable::now()->addHours($hours);
        $upcoming = collect($affectedDates)->filter(fn (CarbonImmutable $d) => $d->endOfDay()->isFuture())->sort()->first();

        if (! $urgent && $upcoming !== null && $upcoming->lt($deadline)) {
            $this->fail(
                'Buổi bị ảnh hưởng ngày '.$upcoming->format('d/m/Y')." còn chưa tới {$hours} giờ.",
                "Phải báo trước tối thiểu {$hours} giờ (BR-TTB-05); trường hợp khẩn cấp thì đánh dấu \"khẩn cấp\" (urgent) để ghi nhận ngoại lệ."
            );
        }

        return $reason;
    }
}
