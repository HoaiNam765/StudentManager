<?php

namespace App\Modules\AcademicYear\Services;

use App\Models\User;
use App\Modules\AcademicYear\Models\MilestoneChangeRequest;
use App\Modules\AcademicYear\Models\MilestoneType;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\AcademicYear\Models\TermMilestone;
use App\Modules\AcademicYear\Models\TermStatus;
use App\Modules\AcademicYear\Notifications\MilestonesChanged;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Support\Audit\AuditLogger;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Notification;

/**
 * Lịch học vụ của học kỳ (FR-ACY-004, 007; BR-ACY-02, 05).
 *
 *     $milestones->date($term, MilestoneType::WithdrawDeadline);   // CarbonImmutable|null — ENR, EXM, GRD, FEE đọc mốc ở đây
 *
 * - Thứ tự hợp lý (BR-ACY-02, MilestoneType::orderRules); sai thì từ chối kèm lý do cụ thể.
 * - Học kỳ chưa bắt đầu: lưu thẳng. Đã bắt đầu: tạo đề nghị có lý do, chờ một người khác có quyền duyệt (ACY.approve)
 *   rồi mới áp dụng, và báo cho người đề nghị (BR-ACY-05).
 * - Sao chép lịch từ học kỳ trước, dời theo ngày bắt đầu của học kỳ mới (FR-ACY-007).
 */
class MilestoneService extends BaseService
{
    public function __construct(
        private readonly AuditLogger $audit,
        private readonly AccessControl $access,
    ) {}

    /** @return array<string, string> loại mốc => ngày (Y-m-d), theo thứ tự của MilestoneType */
    public function milestones(Term $term): array
    {
        $stored = $term->milestones()->get()->mapWithKeys(fn (TermMilestone $m) => [$m->type->value => $m->date->toDateString()]);
        $ordered = [];

        foreach (MilestoneType::cases() as $type) {
            if ($stored->has($type->value)) {
                $ordered[$type->value] = $stored[$type->value];
            }
        }

        return $ordered;
    }

    public function date(Term $term, MilestoneType $type): ?CarbonImmutable
    {
        $value = $this->milestones($term)[$type->value] ?? null;

        return $value === null ? null : CarbonImmutable::parse($value);
    }

    /** Học kỳ đã bắt đầu: tới ngày bắt đầu, hoặc đã qua trạng thái Đăng ký. */
    public function hasStarted(Term $term): bool
    {
        return $term->start_date->toDateString() <= $this->today()
            || in_array($term->status, [TermStatus::IN_PROGRESS, TermStatus::EXAM_GRADING, TermStatus::COMPLETED, TermStatus::LOCKED], true);
    }

    /**
     * Đặt các mốc ($dates: loại => 'Y-m-d', null là bỏ mốc). Học kỳ đã bắt đầu thì trả về đề nghị chờ duyệt.
     *
     * @param  array<string, ?string>  $dates
     */
    public function save(Term $term, array $dates, User $actor, ?string $reason = null): MilestoneChangeRequest|Term
    {
        $this->assertNotLocked($term);

        $current = $this->milestones($term);
        $changes = $this->diff($current, $dates);

        if ($changes === []) {
            return $term;
        }

        $this->assertOrder($term, $this->merge($current, $changes));

        if (! $this->hasStarted($term)) {
            $this->transaction(fn () => $this->apply($term, $changes));

            return $term;
        }

        if ($reason === null || trim($reason) === '') {
            $this->fail(
                "Học kỳ {$term->name} đã bắt đầu nên sửa mốc phải có lý do và người duyệt.",
                'Nhập lý do sửa; đề nghị sẽ chờ một cán bộ khác có quyền duyệt (BR-ACY-05).'
            );
        }

        return MilestoneChangeRequest::create([
            'term_id' => $term->id,
            'changes' => $changes,
            'reason' => $reason,
            'status' => MilestoneChangeRequest::PENDING,
            'requested_by' => $actor->id,
        ]);
    }

    /** Duyệt đề nghị: người duyệt khác người đề nghị và có quyền duyệt ACY; kiểm tra lại thứ tự với lịch hiện tại. */
    public function approve(MilestoneChangeRequest $request, User $approver, ?string $note = null): MilestoneChangeRequest
    {
        $this->assertDecidable($request, $approver);
        $term = $request->term;
        $this->assertNotLocked($term);
        $this->assertOrder($term, $this->merge($this->milestones($term), $request->changes));

        $this->transaction(function () use ($request, $approver, $note, $term): void {
            $reason = "Sửa lịch học vụ đã bắt đầu. Lý do: {$request->reason}. Người đề nghị: {$request->requester->name}. Người duyệt: {$approver->name}.";
            $this->audit->withReason($reason, fn () => $this->apply($term, $request->changes));

            $request->update([
                'status' => MilestoneChangeRequest::APPROVED,
                'decided_by' => $approver->id,
                'decided_at' => now(),
                'decision_note' => $note,
            ]);
        });

        Notification::send($request->requester, new MilestonesChanged($request->refresh(), approved: true));

        return $request;
    }

    public function reject(MilestoneChangeRequest $request, User $approver, string $note): MilestoneChangeRequest
    {
        $this->assertDecidable($request, $approver);

        $request->update([
            'status' => MilestoneChangeRequest::REJECTED,
            'decided_by' => $approver->id,
            'decided_at' => now(),
            'decision_note' => $note,
        ]);

        Notification::send($request->requester, new MilestonesChanged($request->refresh(), approved: false));

        return $request;
    }

    /** Sao chép lịch học vụ từ học kỳ khác, dời theo chênh lệch ngày bắt đầu (FR-ACY-007). */
    public function copyFrom(Term $source, Term $target, User $actor): Term
    {
        if ($source->is($target)) {
            $this->fail('Không sao chép lịch học vụ của một học kỳ sang chính nó.', 'Chọn học kỳ nguồn khác.');
        }

        if ($this->hasStarted($target)) {
            $this->fail("Học kỳ {$target->name} đã bắt đầu nên không sao chép đè lịch học vụ được.", 'Sửa từng mốc (cần lý do và người duyệt).');
        }

        $sourceDates = $this->milestones($source);

        if ($sourceDates === []) {
            $this->fail("Học kỳ {$source->name} chưa có lịch học vụ để sao chép.", 'Chọn học kỳ nguồn đã có lịch học vụ.');
        }

        $offset = (int) $source->start_date->diffInDays($target->start_date, false);
        $shifted = array_map(fn (string $date) => CarbonImmutable::parse($date)->addDays($offset)->toDateString(), $sourceDates);
        // Mốc học kỳ đích đang có mà học kỳ nguồn không có thì bỏ, còn lại lấy theo nguồn
        $dates = array_merge(array_fill_keys(array_keys($this->milestones($target)), null), $shifted);

        return $this->save($target, $dates, $actor);
    }

    // ---------------------------------------------------------------------

    /** @param  array<string, array{old: ?string, new: ?string}>  $changes */
    private function apply(Term $term, array $changes): void
    {
        foreach ($changes as $type => $change) {
            $existing = $term->milestones()->where('type', $type)->first();

            if ($change['new'] === null) {
                // Bỏ mốc: xóa hẳn để có thể đặt lại cùng loại (nhật ký ghi "Xóa vĩnh viễn" kèm ngày cũ)
                $existing?->forceDelete();
            } elseif ($existing !== null) {
                $existing->update(['date' => $change['new']]);
            } else {
                $term->milestones()->create(['type' => $type, 'date' => $change['new']]);
            }
        }
    }

    /**
     * @param  array<string, string>  $current
     * @param  array<string, ?string>  $requested
     * @return array<string, array{old: ?string, new: ?string}>
     */
    private function diff(array $current, array $requested): array
    {
        $changes = [];

        foreach ($requested as $type => $date) {
            if (MilestoneType::tryFrom($type) === null) {
                $this->fail("Không có loại mốc \"{$type}\".", 'Các loại mốc: '.implode(', ', array_map(fn (MilestoneType $t) => $t->value, MilestoneType::cases())).'.');
            }

            $new = $date === null || $date === '' ? null : CarbonImmutable::parse($date)->toDateString();
            $old = $current[$type] ?? null;

            if ($new !== $old) {
                $changes[$type] = ['old' => $old, 'new' => $new];
            }
        }

        return $changes;
    }

    /**
     * @param  array<string, string>  $current
     * @param  array<string, array{old: ?string, new: ?string}>  $changes
     * @return array<string, string>
     */
    private function merge(array $current, array $changes): array
    {
        foreach ($changes as $type => $change) {
            if ($change['new'] === null) {
                unset($current[$type]);
            } else {
                $current[$type] = $change['new'];
            }
        }

        return $current;
    }

    /** @param  array<string, string>  $dates */
    private function assertOrder(Term $term, array $dates): void
    {
        $errors = [];

        foreach (MilestoneType::orderRules() as [$before, $operator, $after]) {
            $a = $dates[$before->value] ?? null;
            $b = $dates[$after->value] ?? null;

            if ($a === null || $b === null) {
                continue;
            }

            if (($operator === '<' && ! ($a < $b)) || ($operator === '<=' && ! ($a <= $b))) {
                $errors[] = $before->label().' ('.$this->format($a).') phải '.($operator === '<' ? 'trước' : 'trước hoặc cùng ngày')
                    .' '.mb_strtolower($after->label()).' ('.$this->format($b).')';
            }
        }

        foreach ([MilestoneType::ClassesStart, MilestoneType::TeachingEnd] as $type) {
            $value = $dates[$type->value] ?? null;

            if ($value !== null && ($value < $term->start_date->toDateString() || $value > $term->end_date->toDateString())) {
                $errors[] = $type->label().' ('.$this->format($value).') phải nằm trong học kỳ ('
                    .$term->start_date->format('d/m/Y').' – '.$term->end_date->format('d/m/Y').')';
            }
        }

        if ($errors !== []) {
            $this->fail('Lịch học vụ chưa đúng thứ tự: '.implode('; ', $errors).'.', 'Sửa các mốc được nêu theo thứ tự hợp lý (BR-ACY-02).');
        }
    }

    private function assertDecidable(MilestoneChangeRequest $request, User $approver): void
    {
        if ($request->status !== MilestoneChangeRequest::PENDING) {
            $this->fail('Đề nghị sửa lịch học vụ này đã được xử lý.', 'Tải lại danh sách đề nghị đang chờ.');
        }

        if ($request->requested_by === $approver->id) {
            $this->fail('Người đề nghị không tự duyệt đề nghị của mình.', 'Nhờ một cán bộ khác có quyền duyệt lịch học vụ (BR-ACY-05).');
        }

        if (! $this->access->allowsGlobally($approver, 'ACY', PermissionAction::Approve)) {
            $this->fail('Bạn không có quyền duyệt sửa lịch học vụ.', 'Cần quyền duyệt (A) của module ACY trên toàn trường.');
        }
    }

    private function assertNotLocked(Term $term): void
    {
        if ($term->status === TermStatus::LOCKED) {
            $this->fail("Học kỳ {$term->name} đã khóa sổ.", 'Mở khóa sổ qua quy trình ngoại lệ (FR-SYS-013) trước khi sửa lịch học vụ.');
        }
    }

    private function format(string $date): string
    {
        return CarbonImmutable::parse($date)->format('d/m/Y');
    }

    private function today(): string
    {
        return now(config('studentmanager.display_timezone'))->toDateString();
    }
}
