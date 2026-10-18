<?php

namespace App\Modules\Curriculum\Services;

use App\Models\User;
use App\Modules\Curriculum\Enums\CurriculumStatus;
use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Models\CurriculumBlock;
use App\Modules\Curriculum\Models\CurriculumSubject;
use App\Modules\Curriculum\Models\ElectiveGroup;
use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\Specialization;
use App\Modules\Faculty\Models\TrainingType;
use App\Modules\Subject\Models\Subject;
use App\Support\Services\BaseService;

/**
 * Xây dựng và ban hành chương trình đào tạo (FR-CUR-001, 002, 003; BR-CUR-02, 03, 04, 06).
 *
 * - CTĐT nháp mới sửa được; đã ban hành thì không sửa trực tiếp mà tạo phiên bản mới (BR-CUR-04, CurriculumVersionService).
 * - Thời gian đào tạo chuẩn khớp với ngành (BR-CUR-06); tổng tín chỉ mặc định theo ngành.
 * - Mỗi học phần chỉ một lần trong CTĐT; tiên quyết phải thuộc CTĐT (hoặc được xác nhận là điều kiện ngoài chương trình)
 *   và có học kỳ dự kiến sớm hơn: khi lưu thì trả về cảnh báo, khi ban hành thì bắt buộc đã xử lý (BR-CUR-03).
 * - Ban hành: từng khối đủ tín chỉ yêu cầu, các khối cộng lại đủ tổng tín chỉ, mỗi nhóm tự chọn có đủ học phần để chọn
 *   (BR-CUR-02); thiếu thì từ chối, nêu rõ thiếu bao nhiêu.
 */
class CurriculumService extends BaseService
{
    public function __construct(
        private readonly CurriculumAnalyzer $analyzer,
        private readonly GraduationRequirementService $graduation,
        private readonly CurriculumVersionService $versions,
    ) {}

    /** @param  array<string, mixed>  $data */
    public function create(array $data): Curriculum
    {
        if (Curriculum::withTrashed()->where('code', $data['code'])->exists()) {
            $this->fail("Mã CTĐT {$data['code']} đã được dùng.", 'Chọn mã khác; muốn sửa CTĐT đã ban hành thì tạo phiên bản mới của mã đó.');
        }

        $major = Major::query()->findOrFail($data['major_id']);
        $data += ['total_credits' => $major->total_credits, 'standard_terms' => $major->standard_terms];
        $this->assertDefinition($data, $major);

        return Curriculum::create($data + ['version' => 1, 'status' => CurriculumStatus::Draft->value]);
    }

    /** @param  array<string, mixed>  $data */
    public function update(Curriculum $curriculum, array $data): Curriculum
    {
        $this->assertDraft($curriculum);

        if (isset($data['code']) && $data['code'] !== $curriculum->code) {
            $this->fail('Không đổi được mã CTĐT.', 'Mã là khóa tra cứu của các phiên bản; sửa tên thì được.');
        }

        $merged = array_merge($curriculum->only(['major_id', 'specialization_id', 'training_type_id', 'cohort_from', 'cohort_to', 'standard_terms', 'total_credits']), $data);
        $this->assertDefinition($merged, Major::query()->findOrFail($merged['major_id']));

        if (isset($data['standard_terms'])) {
            $late = $curriculum->items()->where('planned_term', '>', $data['standard_terms'])->count();

            if ($late > 0) {
                $this->fail("Có {$late} học phần dự kiến ở học kỳ sau học kỳ {$data['standard_terms']}.", 'Chuyển các học phần đó về học kỳ sớm hơn trước khi giảm thời gian đào tạo chuẩn.');
            }
        }

        $curriculum->update($data);

        return $curriculum->refresh();
    }

    /** Xóa CTĐT nháp (kể cả khối, nhóm, học phần). */
    public function delete(Curriculum $curriculum): void
    {
        $this->assertDraft($curriculum);

        $this->transaction(function () use ($curriculum): void {
            $curriculum->items()->get()->each->forceDelete();
            $curriculum->electiveGroups()->get()->each->delete();
            $curriculum->blocks()->get()->each->delete();
            $curriculum->delete();
        });
    }

    // ---------------------------------------------------------------------
    // Khối kiến thức và nhóm tự chọn (FR-CUR-002, 003)
    // ---------------------------------------------------------------------

    /** @param  array{name: string, block_type: string, required_credits: int, sort_order?: int}  $data */
    public function addBlock(Curriculum $curriculum, array $data): CurriculumBlock
    {
        $this->assertDraft($curriculum);

        return CurriculumBlock::create($data + ['curriculum_id' => $curriculum->id, 'sort_order' => $curriculum->blocks()->count() + 1]);
    }

    /** @param  array<string, mixed>  $data */
    public function updateBlock(CurriculumBlock $block, array $data): CurriculumBlock
    {
        $this->assertDraft($block->curriculum);
        $block->update($data);

        return $block->refresh();
    }

    public function removeBlock(CurriculumBlock $block): void
    {
        $this->assertDraft($block->curriculum);

        if ($block->items()->exists() || $block->electiveGroups()->exists()) {
            $this->fail("Khối \"{$block->name}\" còn học phần hoặc nhóm tự chọn.", 'Chuyển hoặc xóa các học phần, nhóm tự chọn của khối trước.');
        }

        $block->delete();
    }

    /** @param  array{curriculum_block_id: int, name: string, min_credits: int}  $data */
    public function addGroup(Curriculum $curriculum, array $data): ElectiveGroup
    {
        $this->assertDraft($curriculum);
        $this->assertBlockOf($curriculum, (int) $data['curriculum_block_id']);

        return ElectiveGroup::create($data + ['curriculum_id' => $curriculum->id]);
    }

    /** @param  array{name?: string, min_credits?: int}  $data */
    public function updateGroup(ElectiveGroup $group, array $data): ElectiveGroup
    {
        $this->assertDraft($group->curriculum);
        $group->update(array_intersect_key($data, array_flip(['name', 'min_credits'])));

        return $group->refresh();
    }

    public function removeGroup(ElectiveGroup $group): void
    {
        $this->assertDraft($group->curriculum);

        if ($group->items()->exists()) {
            $this->fail("Nhóm tự chọn \"{$group->name}\" còn học phần.", 'Xóa hoặc chuyển các học phần của nhóm trước.');
        }

        $group->delete();
    }

    // ---------------------------------------------------------------------
    // Học phần của CTĐT (FR-CUR-003)
    // ---------------------------------------------------------------------

    /**
     * Thêm học phần; trả về kèm cảnh báo tiên quyết liên quan tới học phần này (BR-CUR-03).
     *
     * @param  array{subject_id: int, curriculum_block_id: int, curriculum_elective_group_id?: ?int, planned_term: int, sort_order?: int, outside_prerequisites_confirmed?: bool}  $data
     * @return array{item: CurriculumSubject, warnings: list<string>}
     */
    public function addSubject(Curriculum $curriculum, array $data): array
    {
        $this->assertDraft($curriculum);
        $subject = Subject::query()->findOrFail($data['subject_id']);

        if (! $subject->isActive()) {
            $this->fail("Học phần {$subject->code} đang ngừng hoạt động.", 'Chọn học phần đang hoạt động hoặc học phần thay thế.');
        }

        $existing = $curriculum->items()->where('subject_id', $subject->id)->with('block')->first();

        if ($existing !== null) {
            $this->fail(
                "Học phần {$subject->code} đã có trong CTĐT (khối \"{$existing->block->name}\", học kỳ {$existing->planned_term}).",
                'Mỗi học phần chỉ xuất hiện một lần trong cùng CTĐT (BR-CUR-03); sửa dòng đã có thay vì thêm mới.'
            );
        }

        $data = $this->normalizePlacement($curriculum, $data);

        $item = CurriculumSubject::create($data + [
            'curriculum_id' => $curriculum->id,
            'sort_order' => $curriculum->items()->count() + 1,
        ]);

        return ['item' => $item->load('subject'), 'warnings' => $this->analyzer->prerequisiteWarnings($curriculum, $subject)];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{item: CurriculumSubject, warnings: list<string>}
     */
    public function updateSubject(CurriculumSubject $item, array $data): array
    {
        $curriculum = $item->curriculum;
        $this->assertDraft($curriculum);
        unset($data['subject_id']);

        $data = $this->normalizePlacement($curriculum, array_merge($item->only(['curriculum_block_id', 'curriculum_elective_group_id', 'planned_term']), $data));
        $item->update($data);

        return ['item' => $item->refresh()->load('subject'), 'warnings' => $this->analyzer->prerequisiteWarnings($curriculum, $item->subject)];
    }

    public function removeSubject(CurriculumSubject $item): void
    {
        $this->assertDraft($item->curriculum);
        $item->forceDelete();
    }

    // ---------------------------------------------------------------------
    // Ban hành (BR-CUR-02, 03)
    // ---------------------------------------------------------------------

    public function publish(Curriculum $curriculum, ?User $actor = null): Curriculum
    {
        $this->assertDraft($curriculum);

        $problems = [...$this->analyzer->publishProblems($curriculum), ...$this->graduation->problems($curriculum)];

        if ($problems !== []) {
            $this->fail(
                "Chưa ban hành được CTĐT {$curriculum->code}: ".implode('; ', $problems).'.',
                'Bổ sung học phần hoặc điều chỉnh số tín chỉ yêu cầu, xử lý các cảnh báo tiên quyết rồi ban hành lại (BR-CUR-02, BR-CUR-03).'
            );
        }

        $overlap = Curriculum::query()
            ->inForce()
            ->whereKeyNot($curriculum->id)
            ->where('code', '!=', $curriculum->code)
            ->where('major_id', $curriculum->major_id)
            ->where('training_type_id', $curriculum->training_type_id)
            ->where(fn ($q) => $curriculum->specialization_id === null ? $q->whereNull('specialization_id') : $q->where('specialization_id', $curriculum->specialization_id))
            ->where(fn ($q) => $q->whereNull('cohort_to')->orWhere('cohort_to', '>=', $curriculum->cohort_from))
            ->when($curriculum->cohort_to !== null, fn ($q) => $q->where('cohort_from', '<=', $curriculum->cohort_to))
            ->value('code');

        if ($overlap !== null) {
            $this->fail(
                "CTĐT {$overlap} đang áp dụng cho cùng ngành, hệ đào tạo và các khóa trùng với {$curriculum->code}.",
                'Mỗi sinh viên áp dụng đúng một CTĐT theo ngành và khóa (BR-CUR-01): điều chỉnh khóa áp dụng của một trong hai CTĐT.'
            );
        }

        // Ban hành: lưu cố định điều kiện tốt nghiệp, phiên bản trước (cùng mã) dừng ở khóa liền trước (FR-CUR-005)
        $this->transaction(function () use ($curriculum, $actor): void {
            $curriculum->update([
                'status' => CurriculumStatus::Published,
                'published_at' => now(),
                'published_by' => $actor?->id,
                'effective_from' => $curriculum->effective_from ?? now(config('studentmanager.display_timezone'))->toDateString(),
            ]);

            $this->graduation->freeze($curriculum);
            $this->versions->closePrevious($curriculum);
        });

        return $curriculum->refresh();
    }

    // ---------------------------------------------------------------------

    /** @param  array<string, mixed>  $data */
    private function assertDefinition(array $data, Major $major): void
    {
        if (! $major->isActive()) {
            $this->fail("Ngành {$major->code} đang ngừng hoạt động.", 'Chọn ngành đang hoạt động.');
        }

        if (! empty($data['specialization_id'])) {
            $specialization = Specialization::query()->findOrFail($data['specialization_id']);

            if ($specialization->major_id !== $major->id) {
                $this->fail("Chuyên ngành {$specialization->code} không thuộc ngành {$major->code}.", 'Chọn chuyên ngành của đúng ngành, hoặc để trống nếu CTĐT dùng chung cho cả ngành.');
            }
        }

        $type = TrainingType::query()->findOrFail($data['training_type_id']);

        if (! $type->isActive()) {
            $this->fail("Hệ đào tạo {$type->code} đang ngừng.", 'Chọn hệ đào tạo đang hoạt động.');
        }

        if ((int) $data['standard_terms'] !== $major->standard_terms) {
            $this->fail(
                "Thời gian đào tạo chuẩn của CTĐT ({$data['standard_terms']} học kỳ) khác với ngành {$major->code} ({$major->standard_terms} học kỳ).",
                'Thời gian chuẩn của CTĐT phải khớp với ngành (BR-CUR-06); muốn đổi thì sửa thông tin ngành trước.'
            );
        }

        if (! empty($data['cohort_to']) && (int) $data['cohort_to'] < (int) $data['cohort_from']) {
            $this->fail('Khóa áp dụng đến phải từ khóa áp dụng từ trở về sau.', 'Chọn lại khóa áp dụng, hoặc để trống "đến khóa" nếu chưa giới hạn.');
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizePlacement(Curriculum $curriculum, array $data): array
    {
        $block = $this->assertBlockOf($curriculum, (int) $data['curriculum_block_id']);
        $groupId = $data['curriculum_elective_group_id'] ?? null;

        if ($groupId !== null) {
            $group = $curriculum->electiveGroups()->find($groupId);

            if ($group === null) {
                $this->fail('Nhóm tự chọn không thuộc CTĐT này.', 'Chọn nhóm tự chọn của đúng CTĐT.');
            }

            if ($group->curriculum_block_id !== $block->id) {
                $this->fail("Nhóm tự chọn \"{$group->name}\" thuộc khối khác.", 'Học phần tự chọn phải nằm cùng khối với nhóm tự chọn của nó.');
            }
        }

        $term = (int) $data['planned_term'];

        if ($term < 1 || $term > $curriculum->standard_terms) {
            $this->fail("Học kỳ dự kiến phải từ 1 đến {$curriculum->standard_terms}.", 'Chọn học kỳ trong thời gian đào tạo chuẩn của CTĐT.');
        }

        return $data;
    }

    private function assertBlockOf(Curriculum $curriculum, int $blockId): CurriculumBlock
    {
        $block = $curriculum->blocks()->find($blockId);

        if ($block === null) {
            $this->fail('Khối kiến thức không thuộc CTĐT này.', 'Chọn khối kiến thức của đúng CTĐT.');
        }

        return $block;
    }

    private function assertDraft(Curriculum $curriculum): void
    {
        if (! $curriculum->isDraft()) {
            $this->fail(
                "CTĐT {$curriculum->code} (phiên bản {$curriculum->version}) đã {$curriculum->status->label()} nên không sửa trực tiếp được.",
                'Tạo phiên bản mới của CTĐT cho khóa chỉ định; các khóa cũ giữ phiên bản cũ (BR-CUR-04).'
            );
        }
    }
}
