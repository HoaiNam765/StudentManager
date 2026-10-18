<?php

namespace App\Modules\SchoolClass\Services;

use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\Specialization;
use App\Modules\Faculty\Models\TrainingType;
use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\SchoolClass\Models\Cohort;
use App\Modules\System\Models\PolicySet;
use App\Support\Enums\ActiveStatus;
use App\Support\References\ReferenceRegistry;
use App\Support\Services\BaseService;

/**
 * Khóa tuyển sinh và lớp hành chính (FR-CLS-001, 002; BR-CLS-01).
 *
 * - Khóa: mã và năm bắt đầu duy nhất; năm kết thúc dự kiến không trước năm bắt đầu; bộ quy chế gắn cho khóa phải đã ban
 *   hành và áp dụng cho năm bắt đầu của khóa. Khóa đã có lớp thì không đổi năm bắt đầu, không xóa.
 * - Lớp hành chính: mã duy nhất kể cả lớp đã xóa (BR-CLS-01); thuộc đúng một khóa và một ngành đang hoạt động, chuyên
 *   ngành (nếu có) thuộc ngành đó; sĩ số tối đa trong giới hạn cấu hình. Lớp đã có dữ liệu (sinh viên, cố vấn — #99) thì
 *   không đổi khóa / ngành, không xóa; đóng lớp khi không còn sinh viên đang học.
 */
class AdminClassService extends BaseService
{
    public function __construct(private readonly ReferenceRegistry $references) {}

    // ---------------------------------------------------------------------
    // Khóa (FR-CLS-001)
    // ---------------------------------------------------------------------

    /** @param  array<string, mixed>  $data */
    public function createCohort(array $data): Cohort
    {
        $data['code'] ??= 'K'.$data['start_year'];
        $data['expected_end_year'] ??= (int) $data['start_year'] + (int) config('studentmanager.school_class.default_cohort_years', 4);

        if (Cohort::withTrashed()->where('code', $data['code'])->exists()) {
            $this->fail("Mã khóa {$data['code']} đã được dùng.", 'Mã khóa là duy nhất (BR-CLS-01); chọn mã khác.');
        }

        if (Cohort::withTrashed()->where('start_year', $data['start_year'])->exists()) {
            $this->fail("Đã có khóa bắt đầu năm {$data['start_year']}.", 'Mỗi năm tuyển sinh một khóa; sửa khóa đã có thay vì tạo mới.');
        }

        $this->assertCohortYears($data);
        $this->assertPolicySet($data['policy_set_id'] ?? null, (int) $data['start_year']);

        return Cohort::create($data + ['status' => ActiveStatus::Active->value])->refresh();
    }

    /** @param  array<string, mixed>  $data */
    public function updateCohort(Cohort $cohort, array $data): Cohort
    {
        if (isset($data['code']) && $data['code'] !== $cohort->code) {
            $this->fail('Không đổi được mã khóa.', 'Mã khóa dùng để tra cứu lịch sử; sửa tên hoặc năm kết thúc dự kiến thì được.');
        }

        if (isset($data['start_year']) && (int) $data['start_year'] !== $cohort->start_year) {
            if ($cohort->classes()->withTrashed()->exists()) {
                $this->fail("Khóa {$cohort->code} đã có lớp hành chính nên không đổi năm bắt đầu.", 'Tạo khóa mới cho năm tuyển sinh khác.');
            }

            if (Cohort::withTrashed()->where('start_year', $data['start_year'])->whereKeyNot($cohort->id)->exists()) {
                $this->fail("Đã có khóa bắt đầu năm {$data['start_year']}.", 'Mỗi năm tuyển sinh một khóa.');
            }
        }

        $merged = array_merge($cohort->only('start_year', 'expected_end_year', 'policy_set_id'), $data);
        $this->assertCohortYears($merged);

        if (array_key_exists('policy_set_id', $data)) {
            $this->assertPolicySet($data['policy_set_id'], (int) $merged['start_year']);
        }

        if (isset($data['status']) && $data['status'] === ActiveStatus::Inactive->value) {
            $active = $cohort->classes()->active()->count();

            if ($active > 0) {
                $this->fail("Khóa {$cohort->code} còn {$active} lớp hành chính đang hoạt động.", 'Đóng các lớp của khóa trước, rồi ngừng khóa.');
            }
        }

        $cohort->update($data);

        return $cohort->refresh();
    }

    public function deleteCohort(Cohort $cohort): void
    {
        $classes = $cohort->classes()->withTrashed()->count();

        if ($classes > 0) {
            $this->fail("Khóa {$cohort->code} đã có {$classes} lớp hành chính nên không xóa được.", 'Ngừng hoạt động khóa thay vì xóa (GC-02).');
        }

        $cohort->delete();
    }

    // ---------------------------------------------------------------------
    // Lớp hành chính (FR-CLS-002)
    // ---------------------------------------------------------------------

    /** @param  array<string, mixed>  $data */
    public function createClass(array $data): AdminClass
    {
        if (AdminClass::withTrashed()->where('code', $data['code'])->exists()) {
            $this->fail("Mã lớp {$data['code']} đã được dùng.", 'Mã lớp hành chính là duy nhất, kể cả lớp đã xóa (BR-CLS-01); chọn mã khác.');
        }

        $cohort = Cohort::query()->findOrFail($data['cohort_id']);

        if (! $cohort->isActive()) {
            $this->fail("Khóa {$cohort->code} đang ngừng hoạt động.", 'Chọn khóa đang hoạt động.');
        }

        $data['training_type_id'] ??= TrainingType::default()?->id;
        $data['max_size'] ??= (int) config('studentmanager.school_class.default_max_size', 60);
        $this->assertPlacement($data);
        $this->assertMaxSize((int) $data['max_size']);

        return AdminClass::create($data + ['status' => ActiveStatus::Active->value])->refresh();
    }

    /** @param  array<string, mixed>  $data */
    public function updateClass(AdminClass $class, array $data): AdminClass
    {
        if (isset($data['code']) && $data['code'] !== $class->code) {
            if ($this->references->isReferenced($class)) {
                $this->fail("Lớp {$class->code} đã có dữ liệu liên quan nên không đổi mã.", 'Mã lớp dùng để tra cứu lịch sử; sửa tên thì được.');
            }

            if (AdminClass::withTrashed()->where('code', $data['code'])->whereKeyNot($class->id)->exists()) {
                $this->fail("Mã lớp {$data['code']} đã được dùng.", 'Chọn mã khác (BR-CLS-01).');
            }
        }

        $moved = array_filter(['cohort_id', 'major_id', 'training_type_id'], fn (string $f) => isset($data[$f]) && (int) $data[$f] !== (int) $class->{$f});

        if ($moved !== [] && $this->references->isReferenced($class)) {
            $usage = $this->describe($this->references->usages($class));
            $this->fail("Lớp {$class->code} đã có {$usage} nên không đổi khóa, ngành, hệ.", 'Mỗi lớp thuộc đúng một khóa và một ngành (BR-CLS-01); tạo lớp mới và chuyển sinh viên theo quyết định.');
        }

        $merged = array_merge($class->only('cohort_id', 'major_id', 'specialization_id', 'training_type_id'), $data);

        if ($moved !== [] || array_key_exists('specialization_id', $data)) {
            $this->assertPlacement($merged);
        }

        if (isset($data['max_size'])) {
            $this->assertMaxSize((int) $data['max_size']);
        }

        if (isset($data['status']) && $data['status'] === ActiveStatus::Inactive->value) {
            $active = $this->references->activeUsages($class);

            if ($active !== []) {
                $this->fail("Lớp {$class->code} còn {$this->describe($active)}.", 'Chuyển sinh viên sang lớp khác hoặc kết thúc phân công trước khi đóng lớp.');
            }
        }

        $class->update($data);

        return $class->refresh();
    }

    public function deleteClass(AdminClass $class): void
    {
        $usages = $this->references->usages($class);

        if ($usages !== []) {
            $this->fail("Lớp {$class->code} đã có {$this->describe($usages)} nên không xóa được.", 'Đóng lớp (ngừng hoạt động) thay vì xóa (BR-CLS-06).');
        }

        $class->delete();
    }

    // ---------------------------------------------------------------------

    private function assertCohortYears(array $data): void
    {
        if ((int) $data['expected_end_year'] < (int) $data['start_year']) {
            $this->fail('Năm kết thúc dự kiến trước năm bắt đầu của khóa.', 'Chọn năm kết thúc bằng hoặc sau năm bắt đầu.');
        }
    }

    private function assertPolicySet(?int $policySetId, int $startYear): void
    {
        if ($policySetId === null) {
            return;
        }

        $set = PolicySet::query()->findOrFail($policySetId);

        if (! PolicySet::query()->whereKey($set->id)->published()->forCohort($startYear)->exists()) {
            $this->fail(
                "Bộ quy chế {$set->code} chưa ban hành hoặc không áp dụng cho khóa {$startYear} ({$set->cohortLabel()}).",
                'Chọn bộ quy chế đã ban hành có phạm vi khóa chứa năm bắt đầu, hoặc để trống để hệ thống tự chọn.'
            );
        }
    }

    /** Khóa, ngành, chuyên ngành, hệ đào tạo hợp lệ và đang hoạt động. */
    private function assertPlacement(array $data): void
    {
        $major = Major::query()->findOrFail($data['major_id']);

        if (! $major->isActive()) {
            $this->fail("Ngành {$major->code} đang ngừng hoạt động.", 'Chọn ngành đang hoạt động.');
        }

        if (! empty($data['specialization_id'])) {
            $specialization = Specialization::query()->findOrFail($data['specialization_id']);

            if ($specialization->major_id !== $major->id) {
                $this->fail("Chuyên ngành {$specialization->code} không thuộc ngành {$major->code}.", 'Mỗi lớp thuộc đúng một ngành (BR-CLS-01); chọn chuyên ngành của ngành đó hoặc để trống.');
            }
        }

        $type = TrainingType::query()->findOrFail($data['training_type_id']);

        if (! $type->isActive()) {
            $this->fail("Hệ đào tạo {$type->code} đang ngừng hoạt động.", 'Chọn hệ đào tạo đang hoạt động.');
        }
    }

    private function assertMaxSize(int $size): void
    {
        $limit = (int) config('studentmanager.school_class.admin_class_max_size', 120);

        if ($size < 1 || $size > $limit) {
            $this->fail("Sĩ số tối đa {$size} ngoài giới hạn 1–{$limit}.", 'Nhập sĩ số hợp lý; giới hạn đổi ở tham số hệ thống (studentmanager.school_class).');
        }
    }

    /** @param  array<string, int>  $usages */
    private function describe(array $usages): string
    {
        return implode(', ', array_map(fn ($label, $count) => "{$count} {$label}", array_keys($usages), $usages));
    }
}
