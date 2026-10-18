<?php

namespace App\Modules\Curriculum\Services;

use App\Modules\Curriculum\Enums\CurriculumStatus;
use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Models\CurriculumBlock;
use App\Modules\Curriculum\Models\CurriculumSubject;
use App\Modules\Curriculum\Models\ElectiveGroup;
use App\Modules\Curriculum\Models\GraduationRequirement;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Phiên bản CTĐT (FR-CUR-005; BR-CUR-04).
 *
 * CTĐT đã ban hành không sửa trực tiếp. Muốn thay đổi (đổi học phần, tín chỉ, khối, điều kiện tốt nghiệp) thì tạo phiên
 * bản mới: bản nháp chép toàn bộ khối, nhóm tự chọn, học phần, điều kiện tốt nghiệp của phiên bản đang áp dụng, áp dụng
 * từ một khóa sau khóa bắt đầu của phiên bản cũ. Khi phiên bản mới được ban hành, phiên bản cũ tự giới hạn tới khóa liền
 * trước nên các khóa cũ giữ nguyên CTĐT của mình.
 */
class CurriculumVersionService extends BaseService
{
    /** Tạo phiên bản nháp mới từ phiên bản đã ban hành $source, áp dụng từ khóa $cohortFrom. */
    public function newVersion(Curriculum $source, int $cohortFrom, ?string $effectiveFrom = null): Curriculum
    {
        if ($source->status !== CurriculumStatus::Published) {
            $this->fail('Chỉ tạo phiên bản mới từ CTĐT đang áp dụng.', 'CTĐT còn nháp thì sửa trực tiếp; CTĐT đã ngừng thì chọn phiên bản đang áp dụng.');
        }

        if ($cohortFrom <= $source->cohort_from) {
            $this->fail(
                "Phiên bản mới phải áp dụng từ khóa sau K{$source->cohort_from} (khóa bắt đầu của phiên bản {$source->version}).",
                'Chọn khóa áp dụng muộn hơn; các khóa đã học theo phiên bản cũ giữ nguyên CTĐT của mình (BR-CUR-04).'
            );
        }

        if ($source->cohort_to !== null && $cohortFrom > $source->cohort_to + 1) {
            $this->fail(
                "Phiên bản {$source->version} chỉ áp dụng tới K{$source->cohort_to}; phiên bản mới nên áp dụng từ K".($source->cohort_to + 1).'.',
                'Chọn khóa liền sau để không có khóa nào thiếu CTĐT.'
            );
        }

        $draft = Curriculum::query()->where('code', $source->code)->where('status', CurriculumStatus::Draft->value)->first();

        if ($draft !== null) {
            $this->fail("CTĐT {$source->code} đang có phiên bản {$draft->version} ở trạng thái nháp.", 'Hoàn thiện và ban hành, hoặc xóa phiên bản nháp đó trước khi tạo phiên bản khác.');
        }

        return $this->transaction(function () use ($source, $cohortFrom, $effectiveFrom): Curriculum {
            $version = (int) Curriculum::withTrashed()->where('code', $source->code)->max('version') + 1;

            $copy = Curriculum::create(array_merge(
                $source->only(['code', 'name', 'major_id', 'specialization_id', 'training_type_id', 'total_credits', 'standard_terms', 'description']),
                [
                    'version' => $version,
                    'based_on_id' => $source->id,
                    'cohort_from' => $cohortFrom,
                    'cohort_to' => null,
                    'effective_from' => $effectiveFrom === null ? null : CarbonImmutable::parse($effectiveFrom)->toDateString(),
                    'status' => CurriculumStatus::Draft->value,
                ],
            ));

            $blockMap = [];
            $groupMap = [];

            foreach ($source->blocks()->get() as $block) {
                $blockMap[$block->id] = CurriculumBlock::create($block->only(['name', 'block_type', 'required_credits', 'sort_order']) + ['curriculum_id' => $copy->id])->id;
            }

            foreach ($source->electiveGroups()->get() as $group) {
                $groupMap[$group->id] = ElectiveGroup::create($group->only(['name', 'min_credits']) + [
                    'curriculum_id' => $copy->id,
                    'curriculum_block_id' => $blockMap[$group->curriculum_block_id],
                ])->id;
            }

            foreach ($source->items()->get() as $item) {
                CurriculumSubject::create($item->only(['subject_id', 'planned_term', 'sort_order', 'outside_prerequisites_confirmed']) + [
                    'curriculum_id' => $copy->id,
                    'curriculum_block_id' => $blockMap[$item->curriculum_block_id],
                    'curriculum_elective_group_id' => $item->curriculum_elective_group_id === null ? null : $groupMap[$item->curriculum_elective_group_id],
                ]);
            }

            $requirement = $source->graduationRequirement()->first();

            if ($requirement !== null) {
                GraduationRequirement::create($requirement->only([
                    'min_credits', 'min_cgpa', 'foreign_language_standard', 'it_standard', 'requires_internship',
                    'requires_thesis', 'allows_thesis_alternative', 'no_active_suspension', 'notes',
                ]) + ['curriculum_id' => $copy->id]);
            }

            return $copy->refresh();
        });
    }

    /**
     * Khi ban hành phiên bản $published: phiên bản trước (cùng mã) đang áp dụng được giới hạn tới khóa liền trước.
     */
    public function closePrevious(Curriculum $published): void
    {
        Curriculum::query()
            ->where('code', $published->code)
            ->whereKeyNot($published->id)
            ->where('status', CurriculumStatus::Published->value)
            ->where(fn ($q) => $q->whereNull('cohort_to')->orWhere('cohort_to', '>=', $published->cohort_from))
            ->get()
            ->each(fn (Curriculum $old) => $old->update(['cohort_to' => $published->cohort_from - 1]));
    }

    /** @return Collection<int, Curriculum> các phiên bản cùng mã, mới nhất trước */
    public function versionsOf(Curriculum $curriculum): Collection
    {
        return Curriculum::query()->where('code', $curriculum->code)->orderByDesc('version')->get();
    }
}
