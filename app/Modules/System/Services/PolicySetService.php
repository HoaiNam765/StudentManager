<?php

namespace App\Modules\System\Services;

use App\Models\User;
use App\Modules\System\Enums\PolicySetStatus;
use App\Modules\System\Models\PolicySet;
use App\Modules\System\Settings\PolicyDefinitions;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLogger;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;

/**
 * Soạn và ban hành bộ quy chế đào tạo (FR-SYS-003, BR-SYS-02, BR-SYS-08).
 *
 * Vòng đời: Đang soạn (sửa thoải mái) → Đã ban hành (khóa, không sửa, không xóa).
 * Không hồi tố: chỉ ban hành với ngày hiệu lực từ hôm nay trở đi; muốn đổi quy chế thì tạo phiên bản mới
 * (sao chép từ bộ cũ) có hiệu lực từ ngày sau. Dữ liệu đã tính theo bộ cũ giữ nguyên.
 */
class PolicySetService extends BaseService
{
    public function __construct(
        private readonly AuditLogger $audit,
        private readonly ConfigurationChangeNotifier $notifier,
    ) {}

    /**
     * Tạo bộ quy chế đang soạn. Sao chép giá trị từ $basedOn (thường là bộ đang áp dụng), không có thì dùng mặc định.
     * Cùng mã với bộ đã có thì thành phiên bản tiếp theo.
     *
     * @param  array{code: string, name: string, description?: ?string, cohort_from?: ?int, cohort_to?: ?int, effective_from: string}  $data
     */
    public function createDraft(array $data, ?PolicySet $basedOn = null): PolicySet
    {
        $this->assertCohorts($data['cohort_from'] ?? null, $data['cohort_to'] ?? null);

        return $this->transaction(function () use ($data, $basedOn): PolicySet {
            $version = (int) PolicySet::withTrashed()->where('code', $data['code'])->lockForUpdate()->max('version') + 1;

            $set = PolicySet::create([
                'code' => $data['code'],
                'version' => $version,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'cohort_from' => $data['cohort_from'] ?? null,
                'cohort_to' => $data['cohort_to'] ?? null,
                'effective_from' => $data['effective_from'],
                'status' => PolicySetStatus::Draft,
                'based_on_id' => $basedOn?->id,
            ]);

            $values = PolicyDefinitions::defaults();

            if ($basedOn !== null) {
                $values = array_merge($values, array_intersect_key($basedOn->values(), $values));
            }

            foreach ($values as $key => $value) {
                $set->items()->create(['key' => $key, 'value' => $value]);
            }

            return $set->load('items');
        });
    }

    /** @param  array{name?: string, description?: ?string, cohort_from?: ?int, cohort_to?: ?int, effective_from?: string}  $data */
    public function update(PolicySet $set, array $data): PolicySet
    {
        $this->assertDraft($set);

        $changes = array_intersect_key($data, array_flip(['name', 'description', 'cohort_from', 'cohort_to', 'effective_from']));
        $this->assertCohorts(
            array_key_exists('cohort_from', $changes) ? $changes['cohort_from'] : $set->cohort_from,
            array_key_exists('cohort_to', $changes) ? $changes['cohort_to'] : $set->cohort_to,
        );

        $set->update($changes);

        return $set->refresh()->load('items');
    }

    /**
     * Sửa giá trị tham số của bộ đang soạn.
     *
     * @param  array<string, mixed>  $values
     *
     * @throws ValidationException lỗi theo từng tham số (items.<khóa>)
     */
    public function setItems(PolicySet $set, array $values): PolicySet
    {
        $this->assertDraft($set);

        $errors = [];

        foreach ($values as $key => $value) {
            $error = PolicyDefinitions::errorFor($key, $value);

            if ($error !== null) {
                $errors["items.{$key}"] = $error;
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        $this->transaction(function () use ($set, $values): void {
            foreach ($values as $key => $value) {
                $set->items()->updateOrCreate(['key' => $key], ['value' => $value]);
            }
        });

        return $set->refresh()->load('items');
    }

    /** Ban hành: kiểm tra đầy đủ, hợp lệ, không hồi tố, không trùng bộ khác; ghi nhật ký và báo các ADMIN khác. */
    public function publish(PolicySet $set, User $actor): PolicySet
    {
        $this->assertDraft($set);
        $this->assertCohorts($set->cohort_from, $set->cohort_to);

        $today = CarbonImmutable::now(config('studentmanager.display_timezone'))->toDateString();

        if ($set->effective_from->toDateString() < $today) {
            $this->fail(
                'Không ban hành được bộ quy chế có ngày hiệu lực '.$set->effective_from->format('d/m/Y').' đã qua.',
                'Quy chế không áp dụng hồi tố (BR-SYS-02): chọn ngày hiệu lực từ hôm nay trở đi.'
            );
        }

        $values = $set->load('items')->values();
        $missing = array_keys(array_diff_key(PolicyDefinitions::all(), $values));

        if ($missing !== []) {
            $this->fail('Bộ quy chế còn thiếu tham số: '.implode(', ', $missing).'.', 'Bổ sung đủ các tham số rồi ban hành lại.');
        }

        $errors = [];

        foreach ($values as $key => $value) {
            if (($error = PolicyDefinitions::errorFor($key, $value)) !== null) {
                $errors[] = "{$key}: {$error}";
            }
        }

        $errors = [...$errors, ...PolicyDefinitions::crossErrors($values)];

        if ($errors !== []) {
            $this->fail('Bộ quy chế chưa hợp lệ: '.implode(' ', $errors), 'Sửa các tham số được nêu rồi ban hành lại.');
        }

        $clash = PolicySet::query()
            ->published()
            ->whereDate('effective_from', $set->effective_from)
            ->where('cohort_from', $set->cohort_from)
            ->where('cohort_to', $set->cohort_to)
            ->first();

        if ($clash !== null) {
            $this->fail(
                "Đã có bộ quy chế {$clash->code} v{$clash->version} cho {$clash->cohortLabel()} cùng hiệu lực từ ".$clash->effective_from->format('d/m/Y').'.',
                'Chọn ngày hiệu lực khác (bộ mới hơn sẽ thay bộ cũ từ ngày đó) hoặc sửa phạm vi khóa.'
            );
        }

        $this->transaction(function () use ($set, $actor, $values): void {
            $set->forceFill([
                'status' => PolicySetStatus::Published,
                'published_at' => now(),
                'published_by' => $actor->id,
            ])->save();

            $this->audit->record(AuditEvent::Approved, $set, [], [
                'code' => $set->code,
                'version' => $set->version,
                'cohorts' => $set->cohortLabel(),
                'effective_from' => $set->effective_from->toDateString(),
                'values' => $values,
            ], 'Ban hành bộ quy chế đào tạo');
        });

        $this->notifier->notifyOtherAdmins($actor, "Ban hành bộ quy chế {$set->code} v{$set->version}", [
            "Áp dụng cho {$set->cohortLabel()} từ ngày ".$set->effective_from->format('d/m/Y'),
            $set->based_on_id !== null ? 'Sao chép từ bộ quy chế #'.$set->based_on_id.' và chỉnh sửa' : 'Soạn từ giá trị mặc định',
        ]);

        return $set->refresh()->load('items');
    }

    public function delete(PolicySet $set): void
    {
        $this->assertDraft($set);

        $set->delete();
    }

    private function assertDraft(PolicySet $set): void
    {
        if (! $set->isDraft()) {
            $this->fail(
                "Bộ quy chế {$set->code} v{$set->version} đã ban hành nên không sửa hay xóa được.",
                'Tạo phiên bản mới (sao chép từ bộ này) với ngày hiệu lực mới; bộ cũ vẫn áp dụng cho dữ liệu trước ngày đó.'
            );
        }
    }

    private function assertCohorts(?int $from, ?int $to): void
    {
        if ($from !== null && $to !== null && $from > $to) {
            $this->fail("Khóa bắt đầu (K{$from}) lớn hơn khóa kết thúc (K{$to}).", 'Đổi lại thứ tự, hoặc để trống một đầu nếu không giới hạn.');
        }
    }
}
