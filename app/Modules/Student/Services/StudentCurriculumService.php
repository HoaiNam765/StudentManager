<?php

namespace App\Modules\Student\Services;

use App\Modules\Curriculum\Enums\CurriculumStatus;
use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Services\CurriculumCatalog;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\StudentCurriculum;
use App\Support\Audit\AuditLogger;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;

/**
 * Gán CTĐT cho sinh viên (FR-CUR-006; BR-CUR-01, BR-STU-04).
 *
 * - `assignDefault()`: CTĐT đang áp dụng cho ngành – khóa – hệ (ưu tiên chuyên ngành) qua `CurriculumCatalog::forStudent`;
 *   gọi khi xác nhận nhập học (#102). Không có CTĐT phù hợp thì từ chối kèm hướng dẫn.
 * - `change()`: gán CTĐT khác từ một ngày (chuyển ngành, đổi phiên bản — chỗ cắm cho STU ở P3); CTĐT phải đã ban hành,
 *   đúng ngành – hệ của sinh viên; lần gán cũ kết thúc trước ngày đó; có lý do.
 * - `current()`: CTĐT hiệu lực của sinh viên vào một ngày — ENR, GRA đọc ở đây (BR-CUR-01).
 * Mỗi thời điểm đúng một CTĐT (BR-STU-04); `students.curriculum_id` luôn là CTĐT hiện hành.
 */
class StudentCurriculumService extends BaseService
{
    public function __construct(
        private readonly CurriculumCatalog $catalog,
        private readonly AuditLogger $audit,
    ) {}

    public function assignDefault(Student $student, ?string $from = null): StudentCurriculum
    {
        $curriculum = $this->catalog->forStudent($student->major_id, $student->cohort, $student->training_type_id, $student->specialization_id);

        if ($curriculum === null) {
            $student->loadMissing('major');
            $this->fail(
                "Chưa có CTĐT đang áp dụng cho ngành {$student->major?->code}, khóa {$student->cohort}.",
                'Ban hành CTĐT cho ngành – khóa – hệ đào tạo này trước, rồi gán lại (BR-CUR-01).'
            );
        }

        $current = $this->currentAssignment($student);

        if ($current !== null && $current->curriculum_id === $curriculum->id) {
            return $current;
        }

        return $this->change($student, $curriculum, $from, 'Gán CTĐT theo ngành và khóa');
    }

    public function change(Student $student, Curriculum $curriculum, ?string $from = null, ?string $reason = null): StudentCurriculum
    {
        $from = CarbonImmutable::parse($from ?? $this->today())->toDateString();

        if ($curriculum->status !== CurriculumStatus::Published) {
            $this->fail("CTĐT {$curriculum->code} chưa ban hành hoặc đã ngừng áp dụng.", 'Chỉ gán CTĐT đang áp dụng (BR-CUR-01).');
        }

        if ($curriculum->major_id !== $student->major_id || $curriculum->training_type_id !== $student->training_type_id) {
            $this->fail(
                "CTĐT {$curriculum->code} không thuộc ngành / hệ đào tạo hiện tại của sinh viên.",
                'Chuyển ngành của sinh viên theo quyết định trước, rồi gán CTĐT của ngành mới.'
            );
        }

        $current = $this->currentAssignment($student);

        if ($current !== null && $current->curriculum_id === $curriculum->id) {
            $this->fail("Sinh viên đang học theo CTĐT {$curriculum->code}.", 'Chọn CTĐT khác với CTĐT hiện hành.');
        }

        if ($current !== null && $current->effective_from->toDateString() >= $from) {
            $this->fail(
                "Ngày hiệu lực {$from} phải sau ngày bắt đầu của CTĐT hiện hành ({$current->effective_from->toDateString()}).",
                'Chọn ngày hiệu lực muộn hơn.'
            );
        }

        if ($current !== null && trim((string) $reason) === '') {
            $this->fail('Cần ghi lý do khi đổi CTĐT của sinh viên.', 'Nhập lý do và số quyết định (ví dụ chuyển ngành).');
        }

        return $this->audit->withReason($reason ?: 'Gán CTĐT', fn () => $this->transaction(function () use ($student, $curriculum, $from, $reason, $current): StudentCurriculum {
            $current?->update(['effective_to' => CarbonImmutable::parse($from)->subDay()->toDateString()]);

            $assignment = StudentCurriculum::create([
                'student_id' => $student->id,
                'curriculum_id' => $curriculum->id,
                'effective_from' => $from,
                'reason' => $reason,
            ]);

            $student->update(['curriculum_id' => $curriculum->id]);

            return $assignment->load('curriculum');
        }));
    }

    /** CTĐT hiệu lực của sinh viên vào ngày `$on` (mặc định hôm nay). */
    public function current(Student $student, ?string $on = null): ?Curriculum
    {
        return $this->currentAssignment($student, $on)?->curriculum;
    }

    private function currentAssignment(Student $student, ?string $on = null): ?StudentCurriculum
    {
        return StudentCurriculum::query()
            ->with('curriculum')
            ->where('student_id', $student->id)
            ->when($on !== null, fn ($q) => $q->effectiveOn($on), fn ($q) => $q->whereNull('effective_to'))
            ->orderByDesc('effective_from')
            ->first();
    }

    private function today(): string
    {
        return now(config('studentmanager.display_timezone'))->toDateString();
    }
}
