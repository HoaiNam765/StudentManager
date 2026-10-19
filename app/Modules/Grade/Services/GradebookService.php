<?php

namespace App\Modules\Grade\Services;

use App\Models\User;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Enrollment\Enums\EnrollmentStatus;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Grade\Models\GradeEntry;
use App\Modules\Grade\Models\SectionGradeComponent;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Modules\Subject\Models\GradeComponentTemplate;
use App\Modules\Subject\Services\GradeStructureService;
use App\Support\Services\BaseService;
use Illuminate\Support\Collection;

/** FR-GRD-001/002; BR-GRD-05/10. Mọi kiểm tra nhạy cảm ở máy chủ, ghi trong transaction. */
class GradebookService extends BaseService
{
    public function __construct(
        private readonly AccessControl $access,
        private readonly GradeStructureService $structures,
    ) {}

    public function authorizeView(CourseSection $section, User $user): void
    {
        abort_unless($this->access->allowsOn($user, 'GRD', PermissionAction::View, $section), 403,
            'Bạn không có quyền xem sổ điểm lớp này. Hãy chọn lớp thuộc phạm vi được phân công.');
    }

    public function authorizeStructure(CourseSection $section, User $user): void
    {
        abort_unless($this->access->allowsOn($user, 'GRD', PermissionAction::Update, $section), 403,
            'Bạn không có quyền sửa cơ cấu điểm lớp này. Liên hệ phòng Đào tạo hoặc giảng viên phụ trách.');
    }

    public function authorizeGrading(CourseSection $section, User $user): void
    {
        $this->authorizeStructure($section, $user);
        // Quyền GRD.update phạm vi ALL KHÔNG thay thế điều kiện giảng viên thực sự được phân công.
        abort_unless(SectionInstructor::query()
            ->where('course_section_id', $section->id)
            ->whereHas('lecturer', fn ($q) => $q->where('user_id', $user->id))
            ->exists(), 403,
            'Bạn chưa được phân công giảng dạy lớp này. Hãy liên hệ phòng Đào tạo để được phân công.');
    }

    /** @return Collection<int, SectionGradeComponent> */
    public function components(CourseSection $section): Collection
    {
        return SectionGradeComponent::query()->where('course_section_id', $section->id)
            ->orderBy('sort_order')->get();
    }

    /** @return Collection<int, SectionGradeComponent> */
    public function initialize(CourseSection $section, User $actor): Collection
    {
        $this->authorizeStructure($section, $actor);

        return $this->transaction(function () use ($section): Collection {
            $locked = CourseSection::query()->lockForUpdate()->findOrFail($section->id);
            $existing = $this->components($locked);
            if ($existing->isNotEmpty()) {
                return $existing;
            }

            $this->assertSectionEditable($locked);
            $templates = GradeComponentTemplate::query()->where('subject_id', $locked->subject_id)
                ->orderBy('sort_order')->get();
            if ($templates->isEmpty()) {
                $this->fail('Học phần chưa được cấu hình cơ cấu điểm.',
                    'Yêu cầu phòng Đào tạo thiết lập cơ cấu điểm học phần trước.');
            }

            $this->structures->validate($templates->map(fn ($t) => $t->toComponent())->all());
            foreach ($templates as $i => $template) {
                SectionGradeComponent::create(array_merge($template->toComponent(), [
                    'course_section_id' => $locked->id,
                    'grade_component_template_id' => $template->id,
                    'version' => 1,
                    'sort_order' => $i + 1,
                ]));
            }

            return $this->components($locked);
        });
    }

    /** @param list<array<string, mixed>> $items
     * @return Collection<int, SectionGradeComponent>
     */
    public function replaceStructure(CourseSection $section, User $actor, array $items): Collection
    {
        $this->authorizeStructure($section, $actor);
        $this->structures->validate($items);

        return $this->transaction(function () use ($section, $items): Collection {
            $locked = CourseSection::query()->lockForUpdate()->findOrFail($section->id);
            $this->assertSectionEditable($locked);
            $this->assertNoScores($locked);

            $version = (int) SectionGradeComponent::withTrashed()
                ->where('course_section_id', $locked->id)->max('version') + 1;

            // Giữ lại phiên bản cũ dưới dạng soft delete cho audit, không sửa migration / hard delete.
            $this->components($locked)->each(fn (SectionGradeComponent $c) => $c->delete());

            foreach ($items as $i => $item) {
                SectionGradeComponent::create([
                    'course_section_id' => $locked->id,
                    'version' => $version,
                    'name' => trim($item['name']),
                    'kind' => $item['kind'],
                    'weight' => $item['weight'],
                    'min_score' => $item['min_score'] ?? null,
                    'is_required' => $item['is_required'] ?? true,
                    'is_remote' => $item['is_remote'] ?? false,
                    'sort_order' => $i + 1,
                ]);
            }

            return $this->components($locked);
        });
    }

    /** @param list<array{enrollment_id: int, component_id: int, score: mixed}> $items */
    public function saveDraft(CourseSection $section, User $actor, array $items): int
    {
        $this->authorizeGrading($section, $actor);
        if ($items === []) {
            $this->fail('Danh sách điểm đang trống.', 'Hãy gửi ít nhất một điểm thành phần.');
        }

        return $this->transaction(function () use ($section, $items): int {
            $locked = CourseSection::query()->lockForUpdate()->findOrFail($section->id);
            $this->assertGradingOpen($locked);
            $components = $this->components($locked)->keyBy('id');
            if ($components->isEmpty()) {
                $this->fail('Lớp chưa có cơ cấu điểm.', 'Khởi tạo cơ cấu điểm lớp học phần trước khi nhập.');
            }

            $seen = [];
            foreach ($items as $item) {
                $enrollmentId = (int) $item['enrollment_id'];
                $componentId = (int) $item['component_id'];
                $key = $enrollmentId.':'.$componentId;
                if (isset($seen[$key])) {
                    $this->fail("Trùng điểm của đăng ký #{$enrollmentId}, thành phần #{$componentId}.",
                        'Mỗi sinh viên và thành phần điểm chỉ xuất hiện một lần trong mỗi lần lưu.');
                }
                $seen[$key] = true;

                $component = $components->get($componentId);
                if (! $component) {
                    $this->fail("Thành phần điểm #{$componentId} không thuộc lớp này.",
                        'Tải lại sổ điểm để lấy đúng mã thành phần.');
                }

                $enrollment = Enrollment::query()->whereKey($enrollmentId)
                    ->where('course_section_id', $locked->id)
                    ->whereIn('status', EnrollmentStatus::activeValues())
                    ->first();
                if (! $enrollment) {
                    $this->fail("Đăng ký #{$enrollmentId} không hợp lệ hoặc không thuộc lớp này.",
                        'Chỉ nhập cho sinh viên đăng ký còn hiệu lực, không bị hủy/rút.');
                }

                $value = $this->normalizeScore($item['score']);
                $entry = GradeEntry::query()->firstOrNew([
                    'enrollment_id' => $enrollmentId,
                    'section_grade_component_id' => $componentId,
                ]);
                $entry->fill(['score' => $value, 'status' => $value === null ? 'missing' : 'draft']);
                $entry->save(); // StandardModel tự ghi audit; không dùng query builder update hàng loạt.
            }

            return count($items);
        });
    }

    /** @return array<string, mixed> */
    public function gradebook(CourseSection $section, User $actor): array
    {
        $this->authorizeView($section, $actor);
        $components = $this->components($section);
        $enrollments = Enrollment::query()->active()->where('course_section_id', $section->id)
            ->with('student:id,student_code,full_name')
            ->orderBy('id')->get();
        $entries = GradeEntry::query()
            ->whereIn('enrollment_id', $enrollments->pluck('id'))
            ->whereIn('section_grade_component_id', $components->pluck('id'))
            ->get()->keyBy(fn (GradeEntry $e) => $e->enrollment_id.':'.$e->section_grade_component_id);

        return [
            'section' => $section->only('id', 'code', 'subject_id', 'status'),
            'configured' => $components->isNotEmpty(),
            'components' => $components->map(fn (SectionGradeComponent $c) => [
                'id' => $c->id, 'name' => $c->name, 'kind' => $c->kind->value,
                'weight' => $c->weight, 'min_score' => $c->min_score,
                'is_required' => $c->is_required, 'is_remote' => $c->is_remote,
                'sort_order' => $c->sort_order,
            ])->values(),
            'students' => $enrollments->map(function (Enrollment $enrollment) use ($components, $entries): array {
                $scores = $components->map(function (SectionGradeComponent $component) use ($entries, $enrollment): array {
                    $entry = $entries->get($enrollment->id.':'.$component->id);

                    return [
                        'component_id' => $component->id,
                        'score' => $entry?->score,
                        'status' => $entry?->status ?? 'missing',
                    ];
                });
                $incomplete = $components->isEmpty() || $components->contains(
                    fn (SectionGradeComponent $c) => $c->is_required && $scores->firstWhere('component_id', $c->id)['score'] === null
                );

                return [
                    'enrollment_id' => $enrollment->id,
                    'student' => $enrollment->student?->only('id', 'student_code', 'full_name'),
                    'scores' => $scores->values(),
                    'data_status' => $incomplete ? 'chưa đủ dữ liệu' : 'đủ dữ liệu',
                ];
            })->values(),
        ];
    }

    private function assertNoScores(CourseSection $section): void
    {
        $hasScores = GradeEntry::query()->whereNotNull('score')
            ->whereHas('component', fn ($q) => $q->where('course_section_id', $section->id))
            ->exists();
        if ($hasScores) {
            $this->fail('Không thể sửa cơ cấu điểm vì lớp đã có điểm.',
                'Giữ cơ cấu đã chốt; việc điều chỉnh điểm sau này phải qua quy trình duyệt.');
        }
    }

    private function assertSectionEditable(CourseSection $section): void
    {
        if (in_array($section->status, [SectionStatus::Locked, SectionStatus::Cancelled], true)) {
            $this->fail('Lớp đã khóa hoặc hủy.', 'Chỉ sửa cơ cấu của lớp chưa khóa và chưa bị hủy.');
        }
    }

    private function assertGradingOpen(CourseSection $section): void
    {
        if (! in_array($section->status, [SectionStatus::InProgress, SectionStatus::Finished], true)) {
            $this->fail('Lớp chưa trong giai đoạn nhập điểm hoặc đã bị khóa.',
                'Chỉ nhập khi lớp đang học hoặc đã kết thúc nhưng chưa khóa.');
        }
    }

    private function normalizeScore(mixed $score): ?float
    {
        if ($score === null || $score === '') {
            return null; // Xóa điểm nháp -> chưa đủ dữ liệu, không tự cho bằng 0.
        }
        $min = (float) config('grades.min_score');
        $max = (float) config('grades.max_score');
        $step = (float) config('grades.score_step');
        if (! is_numeric($score) || $step <= 0) {
            $this->fail('Giá trị điểm hoặc cấu hình bước điểm không hợp lệ.',
                'Nhập một số và kiểm tra GRD_SCORE_STEP lớn hơn 0.');
        }
        $value = (float) $score;
        if ($value < $min || $value > $max || abs($value / $step - round($value / $step)) > 0.00001) {
            $this->fail("Điểm {$score} không hợp lệ.",
                "Điểm phải từ {$min} đến {$max}, theo bước {$step}. Ví dụ 7.0, 7.1, 7.2.");
        }

        return round($value, 2);
    }
}
