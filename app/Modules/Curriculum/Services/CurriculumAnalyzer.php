<?php

namespace App\Modules\Curriculum\Services;

use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Models\CurriculumSubject;
use App\Modules\Subject\Enums\RelationType;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Models\SubjectRelation;
use Illuminate\Support\Collection;

/**
 * Phân tích một CTĐT: tín chỉ theo khối (BR-CUR-02) và cảnh báo tiên quyết (BR-CUR-03).
 *
 * Tín chỉ tính vào tổng: học phần bắt buộc cộng mức tối thiểu của các nhóm tự chọn. Học phần điều kiện (không tính GPA,
 * ví dụ giáo dục thể chất, quốc phòng – an ninh) vẫn nằm trong CTĐT nhưng không cộng vào tổng tín chỉ tích lũy.
 */
class CurriculumAnalyzer
{
    /**
     * @return array{total_required: int, blocks_required: int, provided: int, shortfall: int, blocks: list<array<string, mixed>>, groups: list<array<string, mixed>>}
     */
    public function summary(Curriculum $curriculum): array
    {
        $curriculum->load(['blocks', 'electiveGroups', 'items.subject']);
        $blocks = [];
        $groups = [];

        foreach ($curriculum->electiveGroups as $group) {
            $available = $this->credits($curriculum->items->where('curriculum_elective_group_id', $group->id));
            $groups[] = [
                'id' => $group->id,
                'name' => $group->name,
                'block_id' => $group->curriculum_block_id,
                'min_credits' => $group->min_credits,
                'available_credits' => $available,
                'shortfall' => max(0, $group->min_credits - $available),
            ];
        }

        foreach ($curriculum->blocks as $block) {
            $compulsory = $this->credits($curriculum->items->where('curriculum_block_id', $block->id)->whereNull('curriculum_elective_group_id'));
            $elective = (int) $curriculum->electiveGroups->where('curriculum_block_id', $block->id)->sum('min_credits');

            $blocks[] = [
                'id' => $block->id,
                'name' => $block->name,
                'block_type' => $block->block_type->value,
                'required_credits' => $block->required_credits,
                'compulsory_credits' => $compulsory,
                'elective_credits' => $elective,
                'provided_credits' => $compulsory + $elective,
                'shortfall' => max(0, $block->required_credits - $compulsory - $elective),
            ];
        }

        $provided = array_sum(array_column($blocks, 'provided_credits'));

        return [
            'total_required' => $curriculum->total_credits,
            'blocks_required' => (int) $curriculum->blocks->sum('required_credits'),
            'provided' => $provided,
            'shortfall' => max(0, $curriculum->total_credits - $provided),
            'blocks' => $blocks,
            'groups' => $groups,
        ];
    }

    /**
     * Cảnh báo tiên quyết (BR-CUR-03). Có $subject thì chỉ các cảnh báo liên quan tới học phần đó (là học phần hoặc là
     * điều kiện của học phần khác).
     *
     * @return list<string>
     */
    public function prerequisiteWarnings(Curriculum $curriculum, ?Subject $subject = null): array
    {
        $items = $curriculum->items()->with('subject')->get()->keyBy('subject_id');
        $labels = [RelationType::Prerequisite->value => 'tiên quyết', RelationType::Prior->value => 'học trước', RelationType::Corequisite->value => 'song hành'];

        // Một truy vấn cho mọi điều kiện trực tiếp của các học phần trong CTĐT
        $relations = SubjectRelation::query()
            ->whereIn('subject_id', $items->keys())
            ->whereIn('type', array_keys($labels))
            ->when($subject !== null, fn ($q) => $q->where(fn ($w) => $w->where('subject_id', $subject->id)->orWhere('related_subject_id', $subject->id)))
            ->with('relatedSubject')
            ->get()
            ->sortBy(fn (SubjectRelation $r) => [$items[$r->subject_id]->planned_term, $items[$r->subject_id]->subject->code, $r->relatedSubject->code]);

        $warnings = [];

        foreach ($relations as $relation) {
            $warning = $this->checkOne($items[$relation->subject_id], $items->get($relation->related_subject_id), $relation->relatedSubject, $relation->type->value, $labels[$relation->type->value]);

            if ($warning !== null) {
                $warnings[] = $warning;
            }
        }

        return $warnings;
    }

    /**
     * Lý do chưa ban hành được (rỗng = ban hành được).
     *
     * @return list<string>
     */
    public function publishProblems(Curriculum $curriculum): array
    {
        $summary = $this->summary($curriculum);
        $problems = [];

        if ($summary['blocks'] === []) {
            return ['CTĐT chưa có khối kiến thức nào'];
        }

        if ($summary['blocks_required'] < $summary['total_required']) {
            $problems[] = "các khối cộng lại chỉ yêu cầu {$summary['blocks_required']}/{$summary['total_required']} tín chỉ, thiếu ".($summary['total_required'] - $summary['blocks_required']).' tín chỉ';
        }

        if ($summary['shortfall'] > 0) {
            $problems[] = "học phần trong CTĐT mới đủ {$summary['provided']}/{$summary['total_required']} tín chỉ, thiếu {$summary['shortfall']} tín chỉ";
        }

        foreach ($summary['blocks'] as $block) {
            if ($block['shortfall'] > 0) {
                $problems[] = "khối \"{$block['name']}\" mới có {$block['provided_credits']}/{$block['required_credits']} tín chỉ, thiếu {$block['shortfall']}";
            }
        }

        foreach ($summary['groups'] as $group) {
            if ($group['shortfall'] > 0) {
                $problems[] = "nhóm tự chọn \"{$group['name']}\" yêu cầu chọn {$group['min_credits']} tín chỉ nhưng chỉ có {$group['available_credits']} tín chỉ học phần";
            }
        }

        return [...$problems, ...$this->prerequisiteWarnings($curriculum)];
    }

    // ---------------------------------------------------------------------

    private function checkOne(CurriculumSubject $item, ?CurriculumSubject $requiredItem, Subject $required, string $type, string $label): ?string
    {
        $where = "{$item->subject->code} (HK{$item->planned_term})";

        if ($requiredItem === null) {
            return $item->outside_prerequisites_confirmed
                ? null
                : "{$where}: học phần {$label} {$required->code} không thuộc CTĐT";
        }

        $tooLate = $type === 'corequisite'
            ? $requiredItem->planned_term > $item->planned_term
            : $requiredItem->planned_term >= $item->planned_term;

        if (! $tooLate) {
            return null;
        }

        $expected = $type === 'corequisite' ? 'cùng kỳ hoặc sớm hơn' : 'phải sớm hơn';

        return "{$where}: học phần {$label} {$required->code} đang ở HK{$requiredItem->planned_term}, {$expected}";
    }

    /** @param  Collection<int, CurriculumSubject>  $items */
    private function credits(Collection $items): int
    {
        return (int) $items->sum(fn (CurriculumSubject $item) => $item->subject->counts_toward_gpa ? $item->subject->credits : 0);
    }
}
