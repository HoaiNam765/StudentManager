<?php

namespace App\Modules\Subject\Services;

use App\Modules\Subject\Enums\RelationType;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Models\SubjectRelation;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Quan hệ giữa các học phần (FR-SUB-004; BR-SUB-03, 07).
 *
 * - Không có học phần nào là tiên quyết (học trước, song hành) của chính nó; không tạo vòng tiên quyết, kể cả vòng
 *   gián tiếp A → B → C → A (BR-SUB-03). Song hành cho phép học cùng kỳ nên vòng chỉ gồm quan hệ song hành thì hợp lệ;
 *   vòng có ít nhất một quan hệ tiên quyết / học trước thì không thể thỏa mãn và bị chặn.
 * - Giữa hai học phần chỉ có một quan hệ thứ tự (tiên quyết, học trước hoặc song hành).
 * - Tương đương do khoa quyết định, có hiệu lực theo ngày, không trùng thời gian; dùng hai chiều (BR-SUB-07).
 *
 * ENR gọi `requirementsOf()` khi kiểm tra đăng ký; CUR gọi `allPrerequisites()` khi kiểm tra thứ tự học kỳ trong CTĐT.
 */
class SubjectRelationService extends BaseService
{
    /** @param  array{effective_from?: ?string, effective_to?: ?string, decision_no?: ?string, note?: ?string}  $data */
    public function add(Subject $subject, Subject $related, RelationType $type, array $data = []): SubjectRelation
    {
        if ($subject->is($related)) {
            $this->fail("Học phần {$subject->code} không thể có quan hệ {$type->label()} với chính nó.", 'Chọn một học phần khác (BR-SUB-03).');
        }

        foreach ([$subject, $related] as $item) {
            if (! $item->isActive()) {
                $this->fail("Học phần {$item->code} đang ngừng hoạt động.", 'Chỉ khai báo quan hệ giữa các học phần đang hoạt động.');
            }
        }

        if ($type === RelationType::Equivalent) {
            return $this->addEquivalent($subject, $related, $data);
        }

        $existing = SubjectRelation::query()
            ->where('subject_id', $subject->id)
            ->where('related_subject_id', $related->id)
            ->where('type', '!=', RelationType::Equivalent->value)
            ->first();

        if ($existing !== null) {
            $this->fail(
                "{$related->code} đã là học phần {$existing->type->label()} của {$subject->code}.",
                'Giữa hai học phần chỉ có một quan hệ thứ tự; muốn đổi loại thì xóa quan hệ cũ trước.'
            );
        }

        $cycle = $this->cycleIfAdded($subject->id, $related->id, $type);

        if ($cycle !== null) {
            $this->fail(
                "Không thêm được: {$related->code} là {$type->label()} của {$subject->code} sẽ tạo vòng lặp ".$this->describeCycle($cycle).'.',
                'Kiểm tra lại quan hệ tiên quyết; một học phần không thể (trực tiếp hay gián tiếp) là điều kiện của chính nó (BR-SUB-03).'
            );
        }

        return SubjectRelation::create([
            'subject_id' => $subject->id,
            'related_subject_id' => $related->id,
            'type' => $type,
            'note' => $data['note'] ?? null,
        ]);
    }

    /** Kết thúc hiệu lực một quan hệ tương đương (giữ lịch sử). */
    public function end(SubjectRelation $relation, string $endsOn): SubjectRelation
    {
        if ($relation->type !== RelationType::Equivalent) {
            $this->fail('Chỉ quan hệ tương đương mới có ngày hiệu lực.', 'Quan hệ tiên quyết, học trước, song hành không còn dùng thì xóa.');
        }

        $endsOn = CarbonImmutable::parse($endsOn)->toDateString();

        if ($endsOn < $relation->effective_from->toDateString()) {
            $this->fail('Ngày kết thúc phải sau hoặc bằng ngày bắt đầu hiệu lực.', 'Quan hệ chưa có hiệu lực thì xóa thay vì kết thúc.');
        }

        $relation->update(['effective_to' => $endsOn]);

        return $relation->refresh();
    }

    /** Xóa quan hệ (xóa mềm, có nhật ký). Tương đương đã có hiệu lực thì chỉ kết thúc, không xóa (BR-SUB-07). */
    public function remove(SubjectRelation $relation): void
    {
        if ($relation->type === RelationType::Equivalent && $relation->effective_from->toDateString() <= $this->today()) {
            $this->fail('Quan hệ tương đương đã có hiệu lực nên không xóa được.', 'Đặt ngày kết thúc hiệu lực; các ánh xạ đã dùng quan hệ này vẫn tra cứu được.');
        }

        $relation->delete();
    }

    // ---------------------------------------------------------------------
    // Tra cứu cho ENR, CUR, GRA
    // ---------------------------------------------------------------------

    /**
     * Điều kiện trực tiếp của một học phần, theo loại.
     *
     * @return array{prerequisite: Collection<int, Subject>, prior: Collection<int, Subject>, corequisite: Collection<int, Subject>}
     */
    public function requirementsOf(Subject $subject): array
    {
        $relations = SubjectRelation::query()
            ->where('subject_id', $subject->id)
            ->where('type', '!=', RelationType::Equivalent->value)
            ->with('relatedSubject')
            ->get();

        $result = [];

        foreach ([RelationType::Prerequisite, RelationType::Prior, RelationType::Corequisite] as $type) {
            $result[$type->value] = $relations->where('type', $type)->pluck('relatedSubject')->sortBy('code')->values();
        }

        return $result;
    }

    /**
     * Toàn bộ học phần phải học trước (tiên quyết và học trước, kể cả gián tiếp), dùng cho CUR (BR-CUR-03).
     *
     * @return Collection<int, Subject>
     */
    public function allPrerequisites(Subject $subject): Collection
    {
        $edges = $this->edges([RelationType::Prerequisite, RelationType::Prior]);
        $seen = [];
        $stack = [$subject->id];

        while ($stack !== []) {
            foreach ($edges[array_pop($stack)] ?? [] as $next => $strict) {
                if (! isset($seen[$next])) {
                    $seen[$next] = true;
                    $stack[] = $next;
                }
            }
        }

        unset($seen[$subject->id]);

        return Subject::query()->whereKey(array_keys($seen))->orderBy('code')->get();
    }

    /**
     * Học phần tương đương có hiệu lực vào ngày $day (mặc định hôm nay), tính cả hai chiều (BR-SUB-07).
     *
     * @return Collection<int, Subject>
     */
    public function equivalentsOf(Subject $subject, ?string $day = null): Collection
    {
        $day ??= $this->today();

        $ids = SubjectRelation::query()
            ->where('type', RelationType::Equivalent->value)
            ->where(fn ($q) => $q->where('subject_id', $subject->id)->orWhere('related_subject_id', $subject->id))
            ->effectiveOn($day)
            ->get()
            ->map(fn (SubjectRelation $r) => $r->subject_id === $subject->id ? $r->related_subject_id : $r->subject_id)
            ->unique();

        return Subject::query()->whereKey($ids)->orderBy('code')->get();
    }

    // ---------------------------------------------------------------------

    /** @param  array{effective_from?: ?string, effective_to?: ?string, decision_no?: ?string, note?: ?string}  $data */
    private function addEquivalent(Subject $subject, Subject $related, array $data): SubjectRelation
    {
        $from = CarbonImmutable::parse($data['effective_from'] ?? $this->today())->toDateString();
        $to = isset($data['effective_to']) ? CarbonImmutable::parse($data['effective_to'])->toDateString() : null;

        if ($to !== null && $to < $from) {
            $this->fail('Ngày kết thúc hiệu lực phải sau hoặc bằng ngày bắt đầu.', 'Chọn lại ngày, hoặc để trống ngày kết thúc nếu chưa xác định.');
        }

        $overlap = SubjectRelation::query()
            ->where('type', RelationType::Equivalent->value)
            ->where(fn ($q) => $q
                ->where(fn ($d) => $d->where('subject_id', $subject->id)->where('related_subject_id', $related->id))
                ->orWhere(fn ($r) => $r->where('subject_id', $related->id)->where('related_subject_id', $subject->id)))
            ->where(fn ($q) => $q->whereNull('effective_to')->orWhereDate('effective_to', '>=', $from))
            ->when($to !== null, fn ($q) => $q->whereDate('effective_from', '<=', $to))
            ->exists();

        if ($overlap) {
            $this->fail(
                "{$subject->code} và {$related->code} đã tương đương trong khoảng thời gian này.",
                'Kết thúc quan hệ tương đương cũ trước, hoặc chọn ngày hiệu lực không trùng (BR-SUB-07).'
            );
        }

        return SubjectRelation::create([
            'subject_id' => $subject->id,
            'related_subject_id' => $related->id,
            'type' => RelationType::Equivalent,
            'effective_from' => $from,
            'effective_to' => $to,
            'decision_no' => $data['decision_no'] ?? null,
            'note' => $data['note'] ?? null,
        ]);
    }

    /**
     * Thêm cạnh "subject cần related" có tạo vòng không thể thỏa mãn không: tìm đường từ related quay về subject;
     * vòng chỉ gồm song hành thì hợp lệ, có ít nhất một cạnh tiên quyết / học trước thì không.
     *
     * @return list<int>|null Danh sách id theo vòng (bắt đầu và kết thúc ở subject), hoặc null nếu không có vòng
     */
    private function cycleIfAdded(int $subjectId, int $relatedId, RelationType $type): ?array
    {
        $edges = $this->edges([RelationType::Prerequisite, RelationType::Prior, RelationType::Corequisite]);
        $newStrict = $type->isOrdering();

        // Duyệt theo trạng thái (học phần, đã gặp cạnh bắt buộc thứ tự chưa)
        $start = [$relatedId, $newStrict];
        $parent = [$this->key($start) => null];
        $queue = [$start];

        while ($queue !== []) {
            [$node, $strict] = array_shift($queue);

            if ($node === $subjectId && $strict) {
                $path = [];

                for ($k = $this->key([$node, $strict]); $k !== null; $k = $parent[$k]) {
                    $path[] = (int) explode(':', $k)[0];
                }

                return [$subjectId, ...array_reverse($path)];
            }

            foreach ($edges[$node] ?? [] as $next => $edgeStrict) {
                $state = [$next, $strict || $edgeStrict];
                $key = $this->key($state);

                if (! array_key_exists($key, $parent)) {
                    $parent[$key] = $this->key([$node, $strict]);
                    $queue[] = $state;
                }
            }
        }

        return null;
    }

    /**
     * Danh sách kề: id học phần => [id học phần nó cần => cạnh bắt buộc thứ tự?].
     *
     * @param  list<RelationType>  $types
     * @return array<int, array<int, bool>>
     */
    private function edges(array $types): array
    {
        $edges = [];

        SubjectRelation::query()
            ->whereIn('type', array_map(fn (RelationType $t) => $t->value, $types))
            ->get(['subject_id', 'related_subject_id', 'type'])
            ->each(function (SubjectRelation $r) use (&$edges): void {
                $edges[$r->subject_id][$r->related_subject_id] = ($edges[$r->subject_id][$r->related_subject_id] ?? false) || $r->type->isOrdering();
            });

        return $edges;
    }

    /** @param  list<int>  $cycle */
    private function describeCycle(array $cycle): string
    {
        $codes = Subject::withTrashed()->whereKey($cycle)->pluck('code', 'id');

        return implode(' → ', array_map(fn (int $id) => $codes[$id] ?? "#{$id}", $cycle));
    }

    /** @param  array{0: int, 1: bool}  $state */
    private function key(array $state): string
    {
        return $state[0].':'.($state[1] ? 1 : 0);
    }

    private function today(): string
    {
        return now(config('studentmanager.display_timezone'))->toDateString();
    }
}
