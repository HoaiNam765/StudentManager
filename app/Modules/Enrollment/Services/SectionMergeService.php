<?php

namespace App\Modules\Enrollment\Services;

use App\Models\User;
use App\Modules\Enrollment\Enums\EnrollmentStatus;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Services\CourseSectionService;
use App\Modules\Timetable\Services\ScheduleConflictService;
use App\Support\Services\BaseService;
use Illuminate\Support\Facades\DB;

/**
 * Gộp lớp thiếu sĩ số vào lớp khác cùng học phần (BR-CLS-05): trong một giao dịch, chuyển mọi đăng ký còn hiệu lực sang
 * lớp đích (giữ loại đăng ký, nguồn = cán bộ, ghi lý do), rồi hủy lớp nguồn. Lớp đích phải cùng học phần, cùng học kỳ,
 * chưa bắt đầu học và đủ chỗ. Sinh viên bị trùng lịch ở lớp đích vẫn được chuyển nhưng được liệt kê để xử lý tiếp.
 */
class SectionMergeService extends BaseService
{
    public function __construct(
        private readonly CourseSectionService $sections,
        private readonly ScheduleConflictService $conflicts,
        private readonly EnrollmentSectionStudents $students,
    ) {}

    /** @return array{moved: int, clashes: list<string>} */
    public function merge(CourseSection $source, CourseSection $target, string $reason, ?User $actor = null): array
    {
        return $this->transaction(function () use ($source, $target, $reason, $actor): array {
            $locked = CourseSection::query()->whereIn('id', [$source->id, $target->id])->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $source = $locked->get($source->id);
            $target = $locked->get($target->id);

            if ($source->id === $target->id || $source->subject_id !== $target->subject_id || $source->term_id !== $target->term_id) {
                $this->fail("Lớp {$target->code} không cùng học phần / học kỳ với lớp {$source->code}.", 'Chỉ gộp vào lớp khác của cùng học phần trong cùng học kỳ.');
            }

            $open = [SectionStatus::RegistrationOpen, SectionStatus::RegistrationClosed];

            if (! in_array($source->status, $open, true) || ! in_array($target->status, $open, true)) {
                $this->fail('Chỉ gộp các lớp đang Mở hoặc Đóng đăng ký.', 'Lớp đã bắt đầu học hoặc đã hủy thì không gộp được.');
            }

            $moving = Enrollment::query()->where('course_section_id', $source->id)
                ->whereIn('status', [EnrollmentStatus::Registered->value, EnrollmentStatus::Confirmed->value])
                ->lockForUpdate()->get();
            $free = $target->max_size - count($this->students->studentIdsOf($target));

            if ($moving->count() > $free) {
                $this->fail(
                    "Lớp {$target->code} chỉ còn {$free} chỗ, không đủ cho {$moving->count()} sinh viên của lớp {$source->code}.",
                    'Tăng sĩ số tối đa của lớp đích (trong giới hạn phòng) hoặc chọn lớp đích khác.'
                );
            }

            $clashes = [];
            $now = now();

            foreach ($moving as $old) {
                $others = $this->students->otherSectionsOf($old->student_id, $target)->reject(fn (CourseSection $s) => $s->id === $source->id);

                foreach ($others as $other) {
                    if (($clash = $this->conflicts->sectionsClash($target, $other)) !== null) {
                        $clashes[] = "Sinh viên #{$old->student_id}: {$clash}.";
                    }
                }

                $old->update(['status' => EnrollmentStatus::Cancelled, 'cancelled_at' => $now]);
                Enrollment::create([
                    'student_id' => $old->student_id,
                    'course_section_id' => $target->id,
                    'term_id' => $target->term_id,
                    'subject_id' => $target->subject_id,
                    'registration_period_id' => $old->registration_period_id,
                    'type' => $old->type,
                    'status' => $old->status === EnrollmentStatus::Confirmed ? EnrollmentStatus::Confirmed : EnrollmentStatus::Registered,
                    'source' => 'staff',
                    'registered_at' => $old->registered_at,
                    'override_reason' => "Gộp từ lớp {$source->code}: {$reason}",
                    'approved_by' => $actor?->id,
                ]);
            }

            DB::table('enrollment_logs')->insert($moving->map(fn (Enrollment $e) => [
                'student_id' => $e->student_id,
                'course_section_id' => $target->id,
                'action' => 'merge',
                'success' => true,
                'reasons' => json_encode(["Gộp từ lớp {$source->code}: {$reason}"], JSON_UNESCAPED_UNICODE),
                'user_id' => $actor?->id,
                'created_at' => $now,
            ])->all());

            $this->sections->cancel($source, "Gộp vào lớp {$target->code}: {$reason}");

            return ['moved' => $moving->count(), 'clashes' => array_values(array_unique($clashes))];
        });
    }
}
