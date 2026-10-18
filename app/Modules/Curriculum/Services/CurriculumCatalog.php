<?php

namespace App\Modules\Curriculum\Services;

use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Models\CurriculumSubject;
use App\Modules\Subject\Models\Subject;
use Illuminate\Support\Collection;

/**
 * Tra cứu CTĐT cho module khác.
 *
 * - CLS gọi `isInForce($subject)` trước khi mở lớp: chỉ mở lớp cho học phần thuộc ít nhất một CTĐT đang áp dụng
 *   (BR-CUR-07, BR-CLS-03).
 * - STU gọi `forStudent($majorId, $cohort, ...)` để gán CTĐT khi nhập học (BR-CUR-01).
 */
class CurriculumCatalog
{
    /** @return Collection<int, int> id các học phần thuộc ít nhất một CTĐT đang áp dụng */
    public function subjectIdsInForce(): Collection
    {
        return CurriculumSubject::query()
            ->whereIn('curriculum_id', Curriculum::query()->inForce()->select('id'))
            ->distinct()
            ->pluck('subject_id');
    }

    public function isInForce(Subject $subject): bool
    {
        return CurriculumSubject::query()
            ->where('subject_id', $subject->id)
            ->whereIn('curriculum_id', Curriculum::query()->inForce()->select('id'))
            ->exists();
    }

    /** CTĐT đang áp dụng cho ngành – khóa (ưu tiên CTĐT của chuyên ngành nếu có). */
    public function forStudent(int $majorId, int $cohort, int $trainingTypeId, ?int $specializationId = null): ?Curriculum
    {
        return Curriculum::query()
            ->inForce()
            ->forCohort($cohort)
            ->where('major_id', $majorId)
            ->where('training_type_id', $trainingTypeId)
            ->where(fn ($q) => $q->whereNull('specialization_id')->when($specializationId !== null, fn ($s) => $s->orWhere('specialization_id', $specializationId)))
            ->orderByRaw('specialization_id is null')
            ->orderByDesc('version')
            ->first();
    }
}
