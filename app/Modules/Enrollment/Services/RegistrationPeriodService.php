<?php

namespace App\Modules\Enrollment\Services;

use App\Models\User;
use App\Modules\AcademicYear\Models\MilestoneType;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\AcademicYear\Models\TermStatus;
use App\Modules\AcademicYear\Services\MilestoneService;
use App\Modules\Enrollment\Enums\PeriodStatus;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\SchoolClass\Models\Cohort;
use App\Modules\SchoolClass\Models\TrainingFramework;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;

/**
 * Cấu hình đợt đăng ký (FR-ENR-001).
 *
 * - Thời gian nhập theo giờ hiển thị (UTC+7), lưu UTC. Không nhập thì lấy mốc "Mở đăng ký" (00:00) và "Đóng đăng ký"
 *   (23:59) trong lịch học vụ của học kỳ (ACY); không có mốc thì báo cần nhập.
 * - Học kỳ đã kết thúc / khóa thì không mở đợt. Khung tín chỉ riêng của đợt: tối thiểu ≤ tối đa.
 * - Đối tượng: lớp hành chính phải đúng khóa / ngành đã chọn cùng dòng.
 * - Nháp sửa / xóa tự do; đã công bố vẫn sửa được (thời gian, đối tượng); đã chốt thì không sửa.
 */
class RegistrationPeriodService extends BaseService
{
    public function __construct(private readonly MilestoneService $milestones) {}

    /**
     * @param  array{term_id: int, name: string, round: string, opens_at?: ?string, closes_at?: ?string,
     *     min_credits?: ?int, max_credits?: ?int, targets?: list<array{cohort_id?: ?int, major_id?: ?int, admin_class_id?: ?int}>}  $data
     */
    public function create(array $data): RegistrationPeriod
    {
        $term = Term::query()->findOrFail($data['term_id']);
        $attributes = $this->attributes($term, $data);
        if (isset($data['training_framework_id'])) {
            $framework = TrainingFramework::query()->findOrFail($data['training_framework_id']);
            if ($framework->term_id !== $term->id) {
                $this->fail('Chương trình khung không thuộc học kỳ đã chọn.', 'Chọn khung đúng học kỳ của đợt.');
            }
            $attributes['training_framework_id'] = $framework->id;
        }

        return $this->transaction(function () use ($attributes, $data): RegistrationPeriod {
            $period = RegistrationPeriod::create([...$attributes, 'status' => PeriodStatus::Draft]);
            $this->syncTargets($period, $data['targets'] ?? []);

            return $period->load('targets');
        });
    }

    public function update(RegistrationPeriod $period, array $data): RegistrationPeriod
    {
        $this->assertNotFinalized($period);

        $merged = [
            'term_id' => $period->term_id,
            'name' => $data['name'] ?? $period->name,
            'round' => $data['round'] ?? $period->round->value,
            'opens_at' => $data['opens_at'] ?? $this->local($period->opens_at),
            'closes_at' => $data['closes_at'] ?? $this->local($period->closes_at),
            'min_credits' => array_key_exists('min_credits', $data) ? $data['min_credits'] : $period->min_credits,
            'max_credits' => array_key_exists('max_credits', $data) ? $data['max_credits'] : $period->max_credits,
        ];
        $attributes = $this->attributes($period->term, $merged);

        return $this->transaction(function () use ($period, $attributes, $data): RegistrationPeriod {
            $period->update($attributes);

            if (array_key_exists('targets', $data)) {
                $this->syncTargets($period, $data['targets'] ?? []);
            }

            return $period->refresh()->load('targets');
        });
    }

    public function publish(RegistrationPeriod $period, ?User $actor = null): RegistrationPeriod
    {
        return $this->transaction(function () use ($period, $actor): RegistrationPeriod {
            $period = RegistrationPeriod::query()->lockForUpdate()->findOrFail($period->id);
            if ($period->status !== PeriodStatus::Draft) {
                $this->fail("Đợt {$period->name} đang {$period->status->label()}.", 'Chỉ công bố đợt đang ở trạng thái nháp.');
            }

            $this->attributes($period->term, [
                'name' => $period->name, 'round' => $period->round->value,
                'opens_at' => $this->local($period->opens_at), 'closes_at' => $this->local($period->closes_at),
            ]);
            if ($period->training_framework_id !== null && $period->preview_at === null) {
                $this->fail('Cần thông báo danh sách học phần để sinh viên xem trước.', 'Bấm Thông báo xem trước rồi công bố thời gian đăng ký.');
            }
            app(RegistrationOfferingService::class)->activateSections($period);
            $period->update(['status' => PeriodStatus::Published]);
            if ($actor !== null) {
                app(RegistrationOfferingService::class)->notify($period, $actor);
            }

            return $period->refresh();
        });
    }

    public function openEarly(RegistrationPeriod $period, User $actor): RegistrationPeriod
    {
        return $this->transaction(function () use ($period, $actor): RegistrationPeriod {
            $period = RegistrationPeriod::query()->lockForUpdate()->findOrFail($period->id);
            if ($period->status !== PeriodStatus::Published || ! $period->opens_at->isFuture() || ! $period->closes_at->isFuture()) {
                $this->fail('Chỉ mở sớm đợt đã công bố và chưa đến giờ mở.', 'Kiểm tra trạng thái và thời gian của đợt.');
            }
            app(RegistrationOfferingService::class)->assertReady($period);
            $period->update(['opens_at' => now()]);
            app(RegistrationOfferingService::class)->notify($period, $actor);

            return $period->refresh();
        });
    }

    public function delete(RegistrationPeriod $period): void
    {
        if ($period->status !== PeriodStatus::Draft) {
            $this->fail("Đợt {$period->name} đã công bố nên không xóa được.", 'Sửa thời gian đóng của đợt thay vì xóa.');
        }

        $this->transaction(function () use ($period): void {
            if ($period->sections()->exists()) {
                $this->fail('Đợt đã có kế hoạch lớp học phần.', 'Giữ đợt để lưu lịch sử; xóa từng học phần hoặc điều chỉnh thời gian.');
            }
            $period->targets()->delete();
            $period->delete();
        });
    }

    /** @return array<string, mixed> */
    private function attributes(Term $term, array $data): array
    {
        if (in_array($term->status, [TermStatus::COMPLETED, TermStatus::LOCKED], true)) {
            $this->fail("Học kỳ {$term->name} đã kết thúc.", 'Chọn học kỳ chưa kết thúc để mở đợt đăng ký.');
        }

        $opens = $this->resolveTime($term, $data['opens_at'] ?? null, MilestoneType::RegistrationOpen, '00:00');
        $closes = $this->resolveTime($term, $data['closes_at'] ?? null, MilestoneType::RegistrationClose, '23:59');

        if ($opens->gte($closes)) {
            $this->fail('Thời gian mở đợt phải trước thời gian đóng.', 'Nhập lại thời gian mở – đóng (giờ Việt Nam).');
        }

        // Ngày bắt đầu học phải sau ngày đóng đăng ký
        $termStart = CarbonImmutable::parse($term->start_date->toDateString(), $closes->getTimezone());

        if ($closes->startOfDay()->gte($termStart)) {
            $this->fail(
                'Đợt đăng ký đóng ngày '.$closes->format('d/m/Y').", không trước ngày bắt đầu học của {$term->name} (".$termStart->format('d/m/Y').').',
                'Chọn thời gian đóng đăng ký trước ngày '.$termStart->format('d/m/Y').', hoặc mở đợt cho học kỳ sau.'
            );
        }

        $min = isset($data['min_credits']) ? (int) $data['min_credits'] : null;
        $max = isset($data['max_credits']) ? (int) $data['max_credits'] : null;

        if ($min !== null && $max !== null && $min > $max) {
            $this->fail("Tín chỉ tối thiểu ({$min}) lớn hơn tối đa ({$max}).", 'Nhập khung tín chỉ với tối thiểu ≤ tối đa, hoặc để trống để theo quy chế.');
        }

        return [
            'term_id' => $term->id,
            'name' => $data['name'],
            'round' => $data['round'],
            'opens_at' => $opens->utc(),
            'closes_at' => $closes->utc(),
            'min_credits' => $min,
            'max_credits' => $max,
        ];
    }

    private function resolveTime(Term $term, ?string $value, MilestoneType $milestone, string $defaultTime): CarbonImmutable
    {
        $tz = config('studentmanager.display_timezone');

        if ($value !== null) {
            return CarbonImmutable::parse($value, $tz);
        }

        $date = $this->milestones->date($term, $milestone);

        if ($date === null) {
            $this->fail(
                "Lịch học vụ của {$term->name} chưa có mốc \"{$milestone->label()}\".",
                'Nhập thời gian cho đợt hoặc khai báo mốc trong lịch học vụ (ACY) trước.'
            );
        }

        return CarbonImmutable::parse($date->toDateString().' '.$defaultTime, $tz);
    }

    /** @param list<array{cohort_id?: ?int, major_id?: ?int, admin_class_id?: ?int}> $targets */
    private function syncTargets(RegistrationPeriod $period, array $targets): void
    {
        $period->targets()->delete();

        foreach ($targets as $target) {
            $cohortId = $target['cohort_id'] ?? null;
            $majorId = $target['major_id'] ?? null;
            $classId = $target['admin_class_id'] ?? null;

            if ($cohortId === null && $majorId === null && $classId === null) {
                continue;
            }

            if ($classId !== null) {
                $class = AdminClass::query()->findOrFail($classId);

                if (($cohortId !== null && $class->cohort_id !== (int) $cohortId) || ($majorId !== null && $class->major_id !== (int) $majorId)) {
                    $cohort = $cohortId ? Cohort::query()->find($cohortId)?->code : null;
                    $this->fail(
                        "Lớp {$class->code} không thuộc ".($cohort ? "khóa {$cohort}" : 'ngành').' đã chọn cùng dòng.',
                        'Bỏ khóa / ngành ở dòng có lớp hành chính, hoặc chọn đúng lớp.'
                    );
                }
            }

            $period->targets()->create(['cohort_id' => $cohortId, 'major_id' => $majorId, 'admin_class_id' => $classId]);
        }
    }

    private function assertNotFinalized(RegistrationPeriod $period): void
    {
        if ($period->status === PeriodStatus::Finalized) {
            $this->fail("Đợt {$period->name} đã chốt.", 'Sau khi chốt, thay đổi đăng ký do ACAD thực hiện từng trường hợp (BR-ENR-08).');
        }
    }

    private function local(CarbonImmutable $time): string
    {
        return $time->setTimezone(config('studentmanager.display_timezone'))->format('Y-m-d H:i');
    }
}
