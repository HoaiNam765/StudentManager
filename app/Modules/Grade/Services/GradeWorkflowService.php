<?php

namespace App\Modules\Grade\Services;

use App\Models\User;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Grade\Enums\GradePublicationStatus as Status;
use App\Modules\Grade\Events\GradesPublished;
use App\Modules\Grade\Models\SectionGradeWorkflow;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Support\Services\BaseService;
use Illuminate\Validation\ValidationException;

class GradeWorkflowService extends BaseService
{
    public function __construct(private readonly AccessControl $access, private readonly GradebookService $gradebook, private readonly CourseResultService $calculator) {}

    public function current(CourseSection $section): SectionGradeWorkflow
    {
        return SectionGradeWorkflow::query()->firstOrCreate(['course_section_id' => $section->id], ['status' => Status::Draft]);
    }

    public function change(CourseSection $section, User $actor, Status $next, ?string $reason = null, ?string $schedule = null): SectionGradeWorkflow
    {
        $this->authorizeStep($section, $actor, $next);
        return $this->transaction(function () use ($section, $actor, $next, $reason, $schedule) {
            $workflow = $this->current($section);
            $workflow = SectionGradeWorkflow::query()->lockForUpdate()->findOrFail($workflow->id);
            if (! $workflow->status->allows($next, (bool) config('grade_lifecycle.faculty_approval_required', true))) {
                throw ValidationException::withMessages(['status' => "Không thể chuyển từ {$workflow->status->value} sang {$next->value}. Hãy kiểm tra bước duyệt hiện tại."]);
            }
            if ($next === Status::Returned && blank($reason)) {
                throw ValidationException::withMessages(['reason' => 'Khi trả điểm phải ghi rõ lý do để giảng viên điều chỉnh.']);
            }
            if ($next === Status::Submitted) {
                $this->calculator->calculate($section);
            }
            if ($next === Status::Published && $schedule !== null && now()->lt($schedule)) {
                $workflow->scheduled_at = $schedule;
                $workflow->actor_id = $actor->id;
                $workflow->return_reason = null;
                $workflow->save();
                return $workflow;
            }
            $workflow->status = $next;
            $workflow->actor_id = $actor->id;
            $workflow->return_reason = $next === Status::Returned ? $reason : null;
            $workflow->scheduled_at = null;
            if ($next === Status::Published) {
                $workflow->published_at = now();
            }
            $workflow->save();
            if ($next === Status::Published) {
                GradesPublished::dispatch($section->id, $actor->id);
                $this->recalculateGpaForSection($section->id);
            }
            return $workflow;
        });
    }

    public function publishScheduled(): int
    {
        $count = 0;
        SectionGradeWorkflow::query()->where('status', Status::Confirmed->value)
            ->whereNotNull('scheduled_at')->where('scheduled_at', '<=', now())->chunkById(100, function ($rows) use (&$count) {
                foreach ($rows as $row) {
                    $this->transaction(function () use ($row, &$count) {
                        $locked = SectionGradeWorkflow::query()->lockForUpdate()->find($row->id);
                        if (! $locked || $locked->status !== Status::Confirmed || $locked->scheduled_at === null || $locked->scheduled_at->isFuture()) {
                            return;
                        }
                        $locked->status = Status::Published;
                        $locked->published_at = now();
                        $locked->scheduled_at = null;
                        $locked->save();
                        GradesPublished::dispatch($locked->course_section_id, (int) $locked->actor_id);
                        $this->recalculateGpaForSection($locked->course_section_id);
                        $count++;
                    });
                }
            });
        return $count;
    }

    private function recalculateGpaForSection(int $sectionId): void
    {
        $studentIds = \App\Modules\Enrollment\Models\Enrollment::query()
            ->where('course_section_id', $sectionId)
            ->active()
            ->pluck('student_id')
            ->unique();

        if ($studentIds->isNotEmpty()) {
            $gpaService = app(GpaService::class);
            foreach (\App\Modules\Student\Models\Student::whereIn('id', $studentIds)->cursor() as $student) {
                $gpaService->recalculate($student);
            }
        }
    }

    private function authorizeStep(CourseSection $section, User $actor, Status $next): void
    {
        $lecturer = in_array($next, [Status::Submitted, Status::Draft], true);
        if ($lecturer) {
            $this->gradebook->authorizeGrading($section, $actor);
            return;
        }
        if ($next === Status::FacultyApproved) {
            abort_unless($actor->hasRole('DEAN') && $this->access->allowsOn($actor, 'GRD', PermissionAction::Approve, $section), 403, 'Chỉ người có vai trò DEAN và quyền duyệt đúng phạm vi khoa mới được thực hiện.');
            return;
        }
        if ($next === Status::Returned) {
            $isDean = $actor->hasRole('DEAN') && $this->access->allowsOn($actor, 'GRD', PermissionAction::Approve, $section);
            $isAcadExam = ($actor->hasRole('ACAD') || $actor->hasRole('EXAM')) && $this->access->allowsGlobally($actor, 'GRD', PermissionAction::Approve);
            abort_unless($isDean || $isAcadExam, 403, 'Chỉ lãnh đạo Khoa hoặc phòng Đào tạo/Khảo thí mới có quyền trả lại điểm.');
            return;
        }
        abort_unless(($actor->hasRole('ACAD') || $actor->hasRole('EXAM')) && $this->access->allowsGlobally($actor, 'GRD', PermissionAction::Approve), 403, 'Chỉ phòng Đào tạo hoặc Khảo thí có quyền GRD.approve toàn trường được thực hiện.');
    }
}
