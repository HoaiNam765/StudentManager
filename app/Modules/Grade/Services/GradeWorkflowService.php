<?php

namespace App\Modules\Grade\Services;

use App\Models\User;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Enums\GradePublicationStatus as Status;
use App\Modules\Grade\Events\GradesPublished;
use App\Modules\Grade\Models\SectionGradeWorkflow;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;

/**
 * Duyệt và công bố điểm theo lớp học phần (FR-GRD-004, 005; BR-GRD-06, 08; EV-GRD-01).
 *
 * - Nháp → Đã nộp (giảng viên được phân công; mọi sinh viên phải đủ điểm thành phần bắt buộc, kết quả được tính và lưu
 *   vào `course_results` qua CourseGradeService) → Khoa duyệt (DEAN trong phạm vi, khi bật `grade_lifecycle.faculty_approval_required`)
 *   → Đã xác nhận → Đã công bố → Đã khóa (ACAD / EXAM có GRD.approve toàn trường). Trả lại (khoa hoặc ACAD / EXAM) phải
 *   có lý do; giảng viên sửa rồi nộp lại (Trả lại → Nháp → Đã nộp).
 * - Công bố ngay hoặc hẹn giờ (`scheduled_at`); lệnh `grades:publish-scheduled` (mỗi 5 phút) công bố khi tới giờ.
 *   Công bố phát `GradesPublished` (EV-GRD-01) chỉ chứa mã lớp, không kèm điểm.
 * - Từ khi nộp, điểm không sửa trực tiếp (GradebookService chặn); sau công bố điều chỉnh qua yêu cầu (#126).
 */
class GradeWorkflowService extends BaseService
{
    public function __construct(
        private readonly AccessControl $access,
        private readonly GradebookService $gradebook,
        private readonly CourseGradeService $calculator,
    ) {}

    public function current(CourseSection $section): SectionGradeWorkflow
    {
        return SectionGradeWorkflow::query()->firstOrCreate(['course_section_id' => $section->id], ['status' => Status::Draft]);
    }

    /** Lớp đã công bố (hoặc khóa) điểm chưa — sinh viên chỉ xem được khi đã công bố (BR-GRD-08). */
    public function isPublished(int $sectionId): bool
    {
        $status = SectionGradeWorkflow::query()->where('course_section_id', $sectionId)->value('status');

        return $status !== null && Status::from($status instanceof Status ? $status->value : $status)->public();
    }

    public function change(CourseSection $section, User $actor, Status $next, ?string $reason = null, ?string $schedule = null): SectionGradeWorkflow
    {
        $this->authorizeStep($section, $actor, $next);

        return $this->transaction(function () use ($section, $actor, $next, $reason, $schedule): SectionGradeWorkflow {
            $workflow = SectionGradeWorkflow::query()->lockForUpdate()->findOrFail($this->current($section)->id);
            $facultyStep = (bool) config('grade_lifecycle.faculty_approval_required', true);

            if (! $workflow->status->allows($next, $facultyStep)) {
                $this->fail(
                    "Không chuyển được từ \"{$workflow->status->label()}\" sang \"{$next->label()}\".",
                    'Bước tiếp theo hợp lệ: '.$this->nextLabels($workflow->status, $facultyStep).' (FR-GRD-004).'
                );
            }

            if ($next === Status::Returned && blank($reason)) {
                $this->fail('Trả lại bảng điểm phải ghi lý do.', 'Nhập lý do để giảng viên biết cần điều chỉnh gì.');
            }

            if ($next === Status::Submitted) {
                $this->calculateAll($section);
            }

            if ($schedule !== null && ($next !== Status::Published || ! CarbonImmutable::parse($schedule)->isFuture())) {
                $this->fail('Giờ hẹn chỉ dùng khi công bố và phải ở tương lai.', 'Chọn thời điểm sau hiện tại, hoặc bỏ giờ hẹn để công bố ngay (FR-GRD-005).');
            }

            if ($next === Status::Published && $schedule !== null) {
                $workflow->fill(['scheduled_at' => CarbonImmutable::parse($schedule)->utc(), 'actor_id' => $actor->id])->save();

                return $workflow->refresh();
            }

            $workflow->fill([
                'status' => $next,
                'actor_id' => $actor->id,
                'return_reason' => $next === Status::Returned ? $reason : null,
                'scheduled_at' => null,
                'published_at' => $next === Status::Published ? now() : $workflow->published_at,
            ])->save();

            if ($next === Status::Published) {
                GradesPublished::dispatch($section->id, $actor->id);
            }

            return $workflow->refresh();
        });
    }

    /** Công bố các lớp đã hẹn giờ và tới giờ (FR-GRD-005). @return int số lớp đã công bố */
    public function publishScheduled(): int
    {
        $count = 0;

        SectionGradeWorkflow::query()
            ->where('status', Status::Confirmed->value)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->chunkById(100, function ($rows) use (&$count): void {
                foreach ($rows as $row) {
                    $this->transaction(function () use ($row, &$count): void {
                        $locked = SectionGradeWorkflow::query()->lockForUpdate()->find($row->id);

                        if (! $locked || $locked->status !== Status::Confirmed || $locked->scheduled_at === null || $locked->scheduled_at->isFuture()) {
                            return;
                        }

                        $locked->fill(['status' => Status::Published, 'published_at' => now(), 'scheduled_at' => null])->save();
                        GradesPublished::dispatch($locked->course_section_id, (int) $locked->actor_id);
                        $count++;
                    });
                }
            });

        return $count;
    }

    /** Tính và lưu kết quả mọi sinh viên của lớp; còn sinh viên thiếu điểm bắt buộc thì không cho nộp. */
    private function calculateAll(CourseSection $section): void
    {
        $missing = [];

        foreach (Enrollment::query()->active()->with('student:id,student_code')->where('course_section_id', $section->id)->get() as $enrollment) {
            $result = $this->calculator->save($enrollment);

            if ($result['status'] === 'incomplete') {
                $missing[] = $enrollment->student?->student_code ?? "#{$enrollment->id}";
            }
        }

        if ($missing !== []) {
            $this->fail(
                count($missing).' sinh viên chưa đủ điểm thành phần bắt buộc: '.implode(', ', array_slice($missing, 0, 10)).(count($missing) > 10 ? '…' : '').'.',
                'Nhập đủ điểm (hoặc ghi trạng thái đặc biệt) cho các sinh viên này rồi nộp lại.'
            );
        }
    }

    private function nextLabels(Status $from, bool $facultyStep): string
    {
        $labels = array_map(fn (Status $s) => $s->label(), array_filter(Status::cases(), fn (Status $s) => $from->allows($s, $facultyStep)));

        return $labels === [] ? 'không còn bước nào' : implode(', ', $labels);
    }

    private function authorizeStep(CourseSection $section, User $actor, Status $next): void
    {
        if (in_array($next, [Status::Submitted, Status::Draft], true)) {
            $this->gradebook->authorizeGrading($section, $actor);

            return;
        }

        $isDean = $actor->hasRole('DEAN') && $this->access->allowsOn($actor, 'GRD', PermissionAction::Approve, $section);
        $isOffice = ($actor->hasRole('ACAD') || $actor->hasRole('EXAM')) && $this->access->allowsGlobally($actor, 'GRD', PermissionAction::Approve);

        $allowed = match ($next) {
            Status::FacultyApproved => $isDean,
            Status::Returned => $isDean || $isOffice,
            default => $isOffice,
        };

        abort_unless($allowed, 403, match ($next) {
            Status::FacultyApproved => 'Chỉ lãnh đạo khoa (DEAN) phụ trách lớp mới duyệt cấp khoa.',
            Status::Returned => 'Chỉ lãnh đạo khoa hoặc Phòng Đào tạo / Khảo thí mới trả lại bảng điểm.',
            default => 'Chỉ Phòng Đào tạo / Khảo thí (GRD.approve toàn trường) mới xác nhận, công bố, khóa bảng điểm.',
        });
    }
}
