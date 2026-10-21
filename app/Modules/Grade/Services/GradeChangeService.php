<?php

namespace App\Modules\Grade\Services;

use App\Models\User;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Events\GradeAdjusted;
use App\Modules\Grade\Models\CourseResult;
use App\Modules\Grade\Models\GradeChangeRequest;
use App\Modules\Grade\Models\GradeEntry;
use App\Modules\Grade\Models\GradeVersion;
use App\Modules\Grade\Models\SectionGradeWorkflow;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Support\Services\BaseService;
use Illuminate\Validation\ValidationException;

class GradeChangeService extends BaseService
{
    public function __construct(private readonly AccessControl $access, private readonly CourseResultService $calculator) {}

    public function request(Enrollment $enrollment, User $actor, array $items, string $reason, ?string $evidence): GradeChangeRequest
    {
        $section = CourseSection::findOrFail($enrollment->course_section_id);
        $this->assertLecturer($section, $actor);
        $this->assertPublished($section);
        if (GradeChangeRequest::where('enrollment_id', $enrollment->id)->whereIn('status', ['pending_faculty', 'pending_confirmation'])->exists()) {
            throw ValidationException::withMessages(['grades' => 'Đã có yêu cầu điều chỉnh đang chờ duyệt cho lần học này.']);
        }
        $ids = array_column($items, 'component_id');
        if (count($ids) !== count(array_unique($ids))) {
            throw ValidationException::withMessages(['scores' => 'Một thành phần điểm chỉ được điều chỉnh một lần trong yêu cầu.']);
        }
        $actual = GradeEntry::query()->where('enrollment_id', $enrollment->id)->whereIn('section_grade_component_id', $ids)->count();
        if ($actual !== count($items)) {
            throw ValidationException::withMessages(['scores' => 'Có thành phần điểm không thuộc đăng ký này.']);
        }
        $initialStatus = config('grade_lifecycle.faculty_approval_required', true) ? 'pending_faculty' : 'pending_confirmation';
        return GradeChangeRequest::create([
            'enrollment_id' => $enrollment->id, 'requested_by' => $actor->id,
            'proposed_scores' => $items, 'reason' => $reason, 'evidence_path' => $evidence,
            'status' => $initialStatus,
        ]);
    }

    public function decide(GradeChangeRequest $request, User $actor, bool $approve, ?string $reason = null): GradeChangeRequest
    {
        return $this->transaction(function () use ($request, $actor, $approve, $reason) {
            $request = GradeChangeRequest::query()->lockForUpdate()->findOrFail($request->id);
            $enrollment = Enrollment::findOrFail($request->enrollment_id);
            $section = CourseSection::findOrFail($enrollment->course_section_id);
            $this->assertPublished($section);
            if ($request->status === 'pending_faculty') {
                abort_unless($actor->hasRole('DEAN') && $this->access->allowsOn($actor, 'GRD', PermissionAction::Approve, $section), 403, 'Khoa không có quyền duyệt yêu cầu này.');
                $request->faculty_approved_by = $actor->id;
                $request->status = $approve ? 'pending_confirmation' : 'rejected';
            } elseif ($request->status === 'pending_confirmation') {
                abort_unless(($actor->hasRole('ACAD') || $actor->hasRole('EXAM')) && $this->access->allowsGlobally($actor, 'GRD', PermissionAction::Approve), 403, 'Chỉ Đào tạo/Khảo thí được xác nhận điều chỉnh.');
                $request->confirmed_by = $actor->id;
                $request->status = $approve ? 'approved' : 'rejected';
                if ($approve) {
                    $this->apply($request, $enrollment, $section, $actor);
                }
            } else {
                throw ValidationException::withMessages(['status' => 'Yêu cầu này không còn ở trạng thái chờ duyệt.']);
            }
            if (! $approve && blank($reason)) {
                throw ValidationException::withMessages(['reason' => 'Từ chối yêu cầu phải nhập lý do.']);
            }
            if (! $approve) {
                $request->rejection_reason = $reason;
            }
            $request->save();
            return $request;
        });
    }

    private function apply(GradeChangeRequest $request, Enrollment $enrollment, CourseSection $section, User $actor): void
    {
        $previous = GradeEntry::where('enrollment_id', $enrollment->id)->get();
        $previousResult = CourseResult::where('enrollment_id', $enrollment->id)->first();
        $version = (int) GradeVersion::where('enrollment_id', $enrollment->id)->max('version') + 1;
        foreach ($request->proposed_scores as $item) {
            $entry = GradeEntry::where('enrollment_id', $enrollment->id)
                ->where('section_grade_component_id', $item['component_id'])->firstOrFail();
            $entry->update(['score' => $item['score'], 'status' => 'draft']);
        }
        $this->calculator->calculate($section);
        $updatedResult = CourseResult::where('enrollment_id', $enrollment->id)->first();
        GradeVersion::create([
            'enrollment_id' => $enrollment->id, 'grade_change_request_id' => $request->id,
            'version' => $version, 'created_by' => $actor->id, 'reason' => $request->reason,
            'snapshot' => ['before' => ['result' => $previousResult?->only('score_10', 'score_4', 'letter', 'passed', 'special_status'),
                'entries' => $previous->map(fn ($entry) => $entry->only('section_grade_component_id', 'score', 'status'))->all()],
                'after' => ['result' => $updatedResult?->only('score_10', 'score_4', 'letter', 'passed', 'special_status'), 'entries' => GradeEntry::where('enrollment_id', $enrollment->id)->get()->map(fn ($entry) => $entry->only('section_grade_component_id', 'score', 'status'))->all()],
                'proposed_scores' => $request->proposed_scores],
        ]);
        GradeAdjusted::dispatch($enrollment->id, $request->id);
        $student = $enrollment->student ?? \App\Modules\Student\Models\Student::find($enrollment->student_id);
        if ($student) {
            app(GpaService::class)->recalculate($student);
        }
    }

    private function assertPublished(CourseSection $section): void
    {
        $workflow = SectionGradeWorkflow::where('course_section_id', $section->id)->first();
        $statusVal = $workflow?->status instanceof \BackedEnum ? $workflow->status->value : (string) $workflow?->status;
        abort_unless(in_array($statusVal, ['published', 'locked'], true), 422, 'Chỉ điều chỉnh điểm sau công bố hoặc khóa.');
    }

    private function assertLecturer(CourseSection $section, User $actor): void
    {
        abort_unless($this->access->allowsOn($actor, 'GRD', PermissionAction::Update, $section)
            && SectionInstructor::where('course_section_id', $section->id)->whereHas('lecturer', fn ($q) => $q->where('user_id', $actor->id))->exists(),
            403, 'Chỉ giảng viên được phân công mới được yêu cầu điều chỉnh điểm.');
    }
}
