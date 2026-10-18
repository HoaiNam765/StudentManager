<?php

namespace App\Modules\Curriculum\Services;

use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Models\CurriculumSubject;
use App\Modules\Curriculum\Models\GraduationRequirement;
use App\Modules\Subject\Enums\KnowledgeBlock;
use App\Modules\System\Services\PolicyResolver;
use App\Modules\System\Settings\PolicyDefinitions;
use App\Support\Exceptions\BusinessRuleException;
use App\Support\Services\BaseService;

/**
 * Điều kiện tốt nghiệp của CTĐT (FR-CUR-004; BR-CUR-05).
 *
 * Mặc định theo BR-CUR-05: tích lũy đủ tín chỉ của CTĐT, CGPA tối thiểu theo bộ quy chế của khóa (`graduation.min_cgpa`,
 * mặc định 2,00), Đạt mọi học phần điều kiện trong CTĐT, đạt chuẩn đầu ra, không đang bị đình chỉ. Thực tập / khóa luận
 * bắt buộc nếu CTĐT có khối tương ứng. Chỉ sửa khi CTĐT còn nháp; ban hành thì điều kiện được lưu cố định cùng phiên bản.
 * GRA gọi `effectiveFor($ctdt, $khoa)` khi xét tốt nghiệp.
 */
class GraduationRequirementService extends BaseService
{
    public function __construct(private readonly PolicyResolver $policies) {}

    /** Điều kiện đã lưu, hoặc điều kiện mặc định suy ra từ CTĐT (chưa lưu). */
    public function requirementFor(Curriculum $curriculum): GraduationRequirement
    {
        return $curriculum->graduationRequirement()->first() ?? new GraduationRequirement($this->defaults($curriculum));
    }

    /** @param  array<string, mixed>  $data */
    public function save(Curriculum $curriculum, array $data): GraduationRequirement
    {
        if (! $curriculum->isDraft()) {
            $this->fail(
                "CTĐT {$curriculum->code} (phiên bản {$curriculum->version}) đã ban hành nên không sửa điều kiện tốt nghiệp được.",
                'Tạo phiên bản CTĐT mới cho các khóa sau; khóa cũ giữ điều kiện cũ (BR-CUR-04).'
            );
        }

        if (isset($data['min_credits']) && (int) $data['min_credits'] > $curriculum->total_credits) {
            $this->fail(
                "Số tín chỉ tối thiểu để tốt nghiệp ({$data['min_credits']}) lớn hơn tổng tín chỉ của CTĐT ({$curriculum->total_credits}).",
                'Để trống để dùng tổng tín chỉ của CTĐT, hoặc nhập số không vượt tổng tín chỉ.'
            );
        }

        $requirement = $curriculum->graduationRequirement()->first();

        if ($requirement === null) {
            return GraduationRequirement::create(array_merge($this->defaults($curriculum), $data, ['curriculum_id' => $curriculum->id]));
        }

        $requirement->update($data);

        return $requirement->refresh();
    }

    /**
     * Điều kiện đã quy đổi cho một khóa (CGPA trống thì lấy từ bộ quy chế của khóa).
     *
     * @return array{min_credits: int, min_cgpa: float, condition_subjects: list<string>, foreign_language_standard: ?string, it_standard: ?string, requires_internship: bool, requires_thesis: bool, allows_thesis_alternative: bool, no_active_suspension: bool}
     */
    public function effectiveFor(Curriculum $curriculum, int $cohort): array
    {
        $requirement = $this->requirementFor($curriculum);

        if ($requirement->min_cgpa !== null) {
            $minCgpa = (float) $requirement->min_cgpa;
        } else {
            try {
                $minCgpa = (float) $this->policies->value('graduation.min_cgpa', $cohort);
            } catch (BusinessRuleException) {
                $minCgpa = (float) PolicyDefinitions::all()['graduation.min_cgpa']['default'];
            }
        }

        return [
            'min_credits' => $requirement->min_credits ?? $curriculum->total_credits,
            'min_cgpa' => $minCgpa,
            'condition_subjects' => $this->conditionSubjects($curriculum),
            'foreign_language_standard' => $requirement->foreign_language_standard,
            'it_standard' => $requirement->it_standard,
            'requires_internship' => (bool) $requirement->requires_internship,
            'requires_thesis' => (bool) $requirement->requires_thesis,
            'allows_thesis_alternative' => (bool) $requirement->allows_thesis_alternative,
            'no_active_suspension' => (bool) $requirement->no_active_suspension,
        ];
    }

    /**
     * Điều kiện tốt nghiệp không khớp nội dung CTĐT (để chặn ban hành).
     *
     * @return list<string>
     */
    public function problems(Curriculum $curriculum): array
    {
        $requirement = $this->requirementFor($curriculum);
        $types = $curriculum->items()->with('block')->get()->map(fn (CurriculumSubject $i) => $i->block->block_type)->unique();
        $problems = [];

        if ($requirement->requires_internship && ! $types->contains(KnowledgeBlock::Internship)) {
            $problems[] = 'điều kiện tốt nghiệp yêu cầu thực tập nhưng CTĐT chưa có học phần thực tập';
        }

        if ($requirement->requires_thesis && ! $requirement->allows_thesis_alternative && ! $types->contains(KnowledgeBlock::Thesis)) {
            $problems[] = 'điều kiện tốt nghiệp yêu cầu khóa luận / đồ án nhưng CTĐT chưa có học phần tương ứng';
        }

        return $problems;
    }

    /** Lưu cố định điều kiện (mặc định nếu chưa khai báo) khi ban hành. */
    public function freeze(Curriculum $curriculum): GraduationRequirement
    {
        return $curriculum->graduationRequirement()->first()
            ?? GraduationRequirement::create($this->defaults($curriculum) + ['curriculum_id' => $curriculum->id]);
    }

    /** @return list<string> mã các học phần điều kiện (phải Đạt, không tính GPA) trong CTĐT */
    public function conditionSubjects(Curriculum $curriculum): array
    {
        return $curriculum->items()->with('subject')->get()
            ->filter(fn (CurriculumSubject $i) => $i->subject->is_graduation_requirement)
            ->map(fn (CurriculumSubject $i) => $i->subject->code)
            ->sort()->values()->all();
    }

    /** @return array<string, mixed> */
    private function defaults(Curriculum $curriculum): array
    {
        $types = $curriculum->blocks()->pluck('block_type')->map(fn ($t) => $t instanceof KnowledgeBlock ? $t : KnowledgeBlock::from($t));

        return [
            'min_credits' => null,
            'min_cgpa' => null,
            'requires_internship' => $types->contains(KnowledgeBlock::Internship),
            'requires_thesis' => $types->contains(KnowledgeBlock::Thesis),
            'allows_thesis_alternative' => false,
            'no_active_suspension' => true,
        ];
    }
}
