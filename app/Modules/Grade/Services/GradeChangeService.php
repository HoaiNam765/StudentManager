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
use App\Modules\Grade\Models\SectionGradeComponent;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Support\Services\BaseService;

/**
 * Điều chỉnh điểm sau công bố (FR-GRD-006; BR-GRD-06): giảng viên được phân công gửi yêu cầu (lý do, minh chứng, điểm
 * mới từng thành phần) → lãnh đạo khoa duyệt (nếu bật `grade_lifecycle.faculty_approval_required`) → Phòng Đào tạo /
 * Khảo thí xác nhận thì mới áp dụng: ghi điểm, tính lại kết quả (`CourseGradeService`), lưu phiên bản trước – sau
 * (`grade_versions`, bất biến), phát `GradeAdjusted` (EV-GRD-02) để tính lại GPA và báo sinh viên. Từ chối phải có lý do.
 */
class GradeChangeService extends BaseService
{
    public const PENDING = ['pending_faculty', 'pending_confirmation'];

    public function __construct(
        private readonly AccessControl $access,
        private readonly GradebookService $gradebook,
        private readonly GradeWorkflowService $workflows,
        private readonly CourseGradeService $calculator,
    ) {}

    /** @param list<array{component_id: int, score: float|int|string}> $items */
    public function request(Enrollment $enrollment, User $actor, array $items, string $reason, ?string $evidence): GradeChangeRequest
    {
        $section = CourseSection::query()->findOrFail($enrollment->course_section_id);
        $this->gradebook->authorizeGrading($section, $actor);
        $this->assertPublished($section);

        return $this->transaction(function () use ($enrollment, $section, $actor, $items, $reason, $evidence): GradeChangeRequest {
            Enrollment::query()->lockForUpdate()->findOrFail($enrollment->id);

            if (GradeChangeRequest::query()->where('enrollment_id', $enrollment->id)->whereIn('status', self::PENDING)->exists()) {
                $this->fail('Lần học này đã có yêu cầu điều chỉnh đang chờ duyệt.', 'Chờ yêu cầu trước được duyệt / từ chối rồi gửi yêu cầu mới.');
            }

            $components = SectionGradeComponent::query()->where('course_section_id', $section->id)->get()->keyBy('id');
            $current = GradeEntry::query()->where('enrollment_id', $enrollment->id)->get()->keyBy('section_grade_component_id');
            $seen = [];
            $proposed = [];

            foreach ($items as $item) {
                $componentId = (int) $item['component_id'];
                $component = $components->get($componentId);

                if ($component === null) {
                    $this->fail("Thành phần điểm #{$componentId} không thuộc lớp này.", 'Tải lại sổ điểm để lấy đúng mã thành phần.');
                }

                if (isset($seen[$componentId])) {
                    $this->fail("Thành phần \"{$component->name}\" xuất hiện hai lần.", 'Mỗi thành phần điểm chỉ điều chỉnh một lần trong yêu cầu.');
                }

                $seen[$componentId] = true;
                $score = $this->normalizeScore($item['score']);
                $old = $current->get($componentId)?->score;

                if ($old !== null && abs((float) $old - $score) < 0.00001) {
                    $this->fail("Điểm mới của \"{$component->name}\" trùng điểm hiện tại.", 'Bỏ thành phần không đổi khỏi yêu cầu.');
                }

                $proposed[] = ['component_id' => $componentId, 'name' => $component->name, 'old' => $old === null ? null : (float) $old, 'score' => $score];
            }

            return GradeChangeRequest::query()->create([
                'enrollment_id' => $enrollment->id,
                'requested_by' => $actor->id,
                'proposed_scores' => $proposed,
                'reason' => $reason,
                'evidence_path' => $evidence,
                'status' => config('grade_lifecycle.faculty_approval_required', true) ? 'pending_faculty' : 'pending_confirmation',
            ]);
        });
    }

    public function decide(GradeChangeRequest $request, User $actor, bool $approve, ?string $reason = null): GradeChangeRequest
    {
        if (! $approve && blank($reason)) {
            $this->fail('Từ chối yêu cầu điều chỉnh phải nhập lý do.', 'Ghi rõ lý do để giảng viên biết cần bổ sung gì.');
        }

        return $this->transaction(function () use ($request, $actor, $approve, $reason): GradeChangeRequest {
            $request = GradeChangeRequest::query()->lockForUpdate()->findOrFail($request->id);
            $enrollment = Enrollment::query()->findOrFail($request->enrollment_id);
            $section = CourseSection::query()->findOrFail($enrollment->course_section_id);

            if ($request->requested_by === $actor->id) {
                $this->fail('Không tự duyệt yêu cầu điều chỉnh của chính mình.', 'Yêu cầu phải do lãnh đạo khoa / Phòng Đào tạo khác duyệt.');
            }

            if ($request->status === 'pending_faculty') {
                abort_unless($actor->hasRole('DEAN') && $this->access->allowsOn($actor, 'GRD', PermissionAction::Approve, $section), 403,
                    'Chỉ lãnh đạo khoa phụ trách lớp mới duyệt cấp khoa.');
                $request->faculty_approved_by = $actor->id;
                $request->status = $approve ? 'pending_confirmation' : 'rejected';
            } elseif ($request->status === 'pending_confirmation') {
                abort_unless($this->isOffice($actor), 403, 'Chỉ Phòng Đào tạo / Khảo thí (GRD.approve toàn trường) mới xác nhận điều chỉnh điểm.');
                $request->confirmed_by = $actor->id;
                $request->status = $approve ? 'approved' : 'rejected';
            } else {
                $this->fail('Yêu cầu này đã được xử lý.', 'Tải lại danh sách yêu cầu chờ duyệt.');
            }

            $request->rejection_reason = $approve ? null : $reason;
            $request->save();

            if ($request->status === 'approved') {
                $this->apply($request, $enrollment, $actor);
            }

            return $request->refresh();
        });
    }

    /** Lãnh đạo khoa thấy yêu cầu chờ khoa trong phạm vi; Phòng Đào tạo / Khảo thí thấy mọi yêu cầu. */
    public function canReview(User $actor, GradeChangeRequest $request): bool
    {
        $section = $request->enrollment?->section;

        return $this->isOffice($actor)
            || ($section !== null && $actor->hasRole('DEAN') && $this->access->allowsOn($actor, 'GRD', PermissionAction::Approve, $section));
    }

    /** Người này có được duyệt bước hiện tại của yêu cầu không (khoa: chờ khoa; Phòng Đào tạo / Khảo thí: chờ xác nhận). */
    public function canAct(User $actor, GradeChangeRequest $request): bool
    {
        $section = $request->enrollment?->section;

        return $request->requested_by !== $actor->id && match ($request->status) {
            'pending_faculty' => $section !== null && $actor->hasRole('DEAN') && $this->access->allowsOn($actor, 'GRD', PermissionAction::Approve, $section),
            'pending_confirmation' => $this->isOffice($actor),
            default => false,
        };
    }

    private function apply(GradeChangeRequest $request, Enrollment $enrollment, User $actor): void
    {
        $before = $this->snapshot($enrollment);

        foreach ($request->proposed_scores as $item) {
            $entry = GradeEntry::query()->firstOrNew([
                'enrollment_id' => $enrollment->id,
                'section_grade_component_id' => $item['component_id'],
            ]);
            $entry->fill(['score' => $item['score'], 'status' => 'draft'])->save();
        }

        $this->calculator->save($enrollment);

        GradeVersion::query()->create([
            'enrollment_id' => $enrollment->id,
            'grade_change_request_id' => $request->id,
            'version' => (int) GradeVersion::query()->where('enrollment_id', $enrollment->id)->max('version') + 1,
            'reason' => $request->reason,
            'snapshot' => ['before' => $before, 'after' => $this->snapshot($enrollment), 'confirmed_by' => $actor->id],
        ]);

        GradeAdjusted::dispatch($enrollment->id, $request->id);
    }

    /** @return array{result: array<string, mixed>|null, entries: list<array<string, mixed>>} */
    private function snapshot(Enrollment $enrollment): array
    {
        return [
            'result' => CourseResult::query()->where('enrollment_id', $enrollment->id)->first()
                ?->only('score10', 'letter', 'point4', 'is_passed', 'status', 'special_status', 'score_10', 'score_4'), // score_10, score_4: tên khóa view FE admin/grade/history
            'entries' => GradeEntry::query()->where('enrollment_id', $enrollment->id)->orderBy('section_grade_component_id')->get()
                ->map(fn (GradeEntry $e) => ['component_id' => $e->section_grade_component_id, 'score' => $e->score])->all(),
        ];
    }

    private function assertPublished(CourseSection $section): void
    {
        if (! $this->workflows->isPublished($section->id)) {
            $this->fail('Bảng điểm lớp chưa công bố nên không cần yêu cầu điều chỉnh.',
                'Trước công bố, đề nghị trả lại bảng điểm rồi sửa trực tiếp trong sổ điểm (BR-GRD-06).');
        }
    }

    private function isOffice(User $actor): bool
    {
        return ($actor->hasRole('ACAD') || $actor->hasRole('EXAM')) && $this->access->allowsGlobally($actor, 'GRD', PermissionAction::Approve);
    }

    private function normalizeScore(mixed $score): float
    {
        $min = (float) config('grades.min_score', 0);
        $max = (float) config('grades.max_score', 10);
        $step = (float) config('grades.score_step', 0.1);

        if (! is_numeric($score) || (float) $score < $min || (float) $score > $max || abs((float) $score / $step - round((float) $score / $step)) > 0.00001) {
            $this->fail("Điểm {$score} không hợp lệ.", "Điểm phải từ {$min} đến {$max}, theo bước {$step}.");
        }

        return (float) $score;
    }
}
