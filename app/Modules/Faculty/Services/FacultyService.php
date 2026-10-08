<?php

namespace App\Modules\Faculty\Services;

use App\Modules\Faculty\Enums\EducationLevel;
use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\Specialization;
use App\Modules\Faculty\Models\TrainingType;
use App\Support\References\ReferenceRegistry;
use App\Support\Services\BaseService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Cơ cấu tổ chức đào tạo (FR-FAC-001..005; BR-FAC-01, 02, 05, GC-05).
 *
 * - Mã khoa, bộ môn, ngành, chuyên ngành duy nhất và không cấp lại; không đổi mã khi đã có dữ liệu liên quan (BR-FAC-01).
 * - Bộ môn, ngành thuộc đúng một khoa; chuyên ngành thuộc đúng một ngành; không đổi đơn vị cấp trên (BR-FAC-02,
 *   chuyển đơn vị là FR-FAC-009). Không tạo đơn vị con dưới đơn vị đã ngừng.
 * - Trạng thái Hoạt động / Ngừng thay cho xóa; ngừng khoa còn bộ môn hoặc ngành đang hoạt động thì bị từ chối kèm
 *   danh sách dữ liệu cần chuyển (BR-FAC-05). Chỉ xóa (mềm) được đơn vị chưa có dữ liệu liên quan.
 */
class FacultyService extends BaseService
{
    public function __construct(private readonly ReferenceRegistry $references) {}

    /** @param  array<string, mixed>  $data */
    public function createFaculty(array $data): Faculty
    {
        $this->assertCodeFree(Faculty::class, $data['code'], 'khoa');

        return Faculty::create($data);
    }

    /** @param  array<string, mixed>  $data */
    public function createDepartment(array $data): Department
    {
        $this->assertParentActive(Faculty::query()->findOrFail($data['faculty_id']), 'bộ môn');
        $this->assertCodeFree(Department::class, $data['code'], 'bộ môn');

        return Department::create($data);
    }

    /** @param  array<string, mixed>  $data */
    public function createMajor(array $data): Major
    {
        $this->assertParentActive(Faculty::query()->findOrFail($data['faculty_id']), 'ngành');
        $this->assertCodeFree(Major::class, $data['code'], 'ngành');

        // Gán sẵn mặc định để model trả về có đủ giá trị (không đọc lại mặc định của CSDL)
        $data['education_level'] ??= EducationLevel::Undergraduate->value;

        return Major::create($data);
    }

    /** @param  array<string, mixed>  $data */
    public function createSpecialization(array $data): Specialization
    {
        $this->assertParentActive(Major::query()->findOrFail($data['major_id']), 'chuyên ngành');
        $this->assertCodeFree(Specialization::class, $data['code'], 'chuyên ngành');

        return Specialization::create($data);
    }

    /** @param  array{code: string, name: string, description?: ?string, is_default?: bool}  $data */
    public function createTrainingType(array $data): TrainingType
    {
        $this->assertCodeFree(TrainingType::class, $data['code'], 'hệ đào tạo');

        return $this->transaction(function () use ($data): TrainingType {
            $type = TrainingType::create(array_merge($data, ['is_default' => false]));

            if (! empty($data['is_default']) || TrainingType::default() === null) {
                $this->makeDefault($type);
            }

            return $type->refresh();
        });
    }

    /**
     * Sửa khoa, bộ môn, ngành hoặc chuyên ngành; `status` = active / inactive để kích hoạt hoặc ngừng.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Faculty|Department|Major|Specialization $unit, array $data): Faculty|Department|Major|Specialization
    {
        $label = $this->label($unit);

        if (isset($data['code']) && $data['code'] !== $unit->code) {
            $related = $this->relatedData($unit);

            if ($related !== []) {
                $this->fail(
                    "Không đổi được mã {$label} {$unit->code} vì đã có dữ liệu liên quan (".ReferenceRegistry::describe($related).').',
                    'Đổi tên thì được; đổi mã cần quy trình đặc biệt (BR-FAC-01) vì các dữ liệu khác đang tham chiếu mã này.'
                );
            }

            $this->assertCodeFree($unit::class, $data['code'], $label);
        }

        $status = $data['status'] ?? null;
        unset($data['status']);

        $this->transaction(function () use ($unit, $data, $status): void {
            $unit->update($data);

            match ($status) {
                'inactive' => $this->deactivate($unit),
                'active' => $this->activate($unit),
                default => null,
            };
        });

        return $unit->refresh();
    }

    /** @param  array{name?: string, description?: ?string, is_default?: bool, status?: string}  $data */
    public function updateTrainingType(TrainingType $type, array $data): TrainingType
    {
        $this->transaction(function () use ($type, $data): void {
            $type->update(array_intersect_key($data, array_flip(['name', 'description'])));

            if (! empty($data['is_default'])) {
                if (! $type->isActive() && ($data['status'] ?? null) !== 'active') {
                    $this->fail("Hệ đào tạo {$type->code} đang ngừng.", 'Kích hoạt lại trước khi đặt làm mặc định.');
                }

                $this->makeDefault($type);
            }

            if (($data['status'] ?? null) === 'inactive') {
                if ($type->refresh()->is_default) {
                    $this->fail("Không ngừng được hệ đào tạo mặc định {$type->code}.", 'Đặt hệ khác làm mặc định trước.');
                }

                $usages = $this->references->activeUsages($type);

                if ($usages !== []) {
                    $this->fail("Hệ đào tạo {$type->code} còn ".ReferenceRegistry::describe($usages).'.', 'Chuyển các dữ liệu này sang hệ khác trước khi ngừng.');
                }

                $type->deactivate();
            } elseif (($data['status'] ?? null) === 'active') {
                $type->activate();
            }
        });

        return $type->refresh();
    }

    /** Xóa (mềm) đơn vị chưa có dữ liệu liên quan; đã có thì chỉ ngừng hoạt động (BR-FAC-05, GC-05). */
    public function delete(Faculty|Department|Major|Specialization $unit): void
    {
        $related = $this->relatedData($unit);

        if ($related !== []) {
            $this->fail(
                "Không xóa được {$this->label($unit)} {$unit->code} vì đã có dữ liệu liên quan (".ReferenceRegistry::describe($related).').',
                'Hãy ngừng hoạt động đơn vị thay cho việc xóa; dữ liệu lịch sử vẫn hiển thị đúng.'
            );
        }

        $unit->delete();
    }

    /**
     * Dữ liệu cần chuyển trước khi ngừng hoạt động: đơn vị con đang hoạt động và dữ liệu còn hoạt động của module khác.
     *
     * @return array<string, list<string>|int> ['bộ môn đang hoạt động' => ['BM-KTPM'], 'giảng viên đang làm việc' => 12]
     */
    public function dependentsBlockingDeactivation(Faculty|Department|Major|Specialization $unit): array
    {
        $blocking = [];

        if ($unit instanceof Faculty) {
            $blocking['bộ môn đang hoạt động'] = $unit->departments()->active()->orderBy('code')->pluck('code')->all();
            $blocking['ngành đang hoạt động'] = $unit->majors()->active()->orderBy('code')->pluck('code')->all();
        }

        if ($unit instanceof Major) {
            $blocking['chuyên ngành đang hoạt động'] = $unit->specializations()->active()->orderBy('code')->pluck('code')->all();
        }

        $blocking = array_filter($blocking);

        return $blocking + $this->references->activeUsages($unit);
    }

    // ---------------------------------------------------------------------

    private function deactivate(Faculty|Department|Major|Specialization $unit): void
    {
        $blocking = $this->dependentsBlockingDeactivation($unit);

        if ($blocking !== []) {
            $parts = [];

            foreach ($blocking as $label => $value) {
                $parts[] = is_array($value) ? "{$label}: ".implode(', ', $value) : "{$value} {$label}";
            }

            $this->fail(
                "Không thể ngừng hoạt động {$this->label($unit)} {$unit->code} vì còn dữ liệu cần chuyển: ".implode('; ', $parts).'.',
                'Chuyển các dữ liệu trên sang đơn vị khác hoặc ngừng chúng trước, rồi ngừng đơn vị này (BR-FAC-05).'
            );
        }

        $unit->deactivate();
    }

    private function activate(Faculty|Department|Major|Specialization $unit): void
    {
        $parent = match (true) {
            $unit instanceof Department, $unit instanceof Major => $unit->faculty,
            $unit instanceof Specialization => $unit->major,
            default => null,
        };

        if ($parent !== null) {
            $this->assertParentActive($parent, $this->label($unit));
        }

        $unit->activate();
    }

    /** @return array<string, int> */
    private function relatedData(Faculty|Department|Major|Specialization $unit): array
    {
        $related = match (true) {
            $unit instanceof Faculty => [
                'bộ môn' => $unit->departments()->withTrashed()->count(),
                'ngành' => $unit->majors()->withTrashed()->count(),
            ],
            $unit instanceof Major => ['chuyên ngành' => $unit->specializations()->withTrashed()->count()],
            default => [],
        };

        return array_filter($related) + $this->references->usages($unit);
    }

    private function makeDefault(TrainingType $type): void
    {
        TrainingType::query()->whereKeyNot($type->id)->where('is_default', true)->lockForUpdate()->get()
            ->each(fn (TrainingType $other) => $other->update(['is_default' => false]));

        $type->update(['is_default' => true]);
    }

    private function assertParentActive(Faculty|Major $parent, string $childLabel): void
    {
        if (! $parent->isActive()) {
            $this->fail(
                Str::ucfirst($this->label($parent))." {$parent->code} đang ngừng hoạt động nên không thêm hay kích hoạt {$childLabel} được.",
                "Chọn {$this->label($parent)} đang hoạt động hoặc kích hoạt lại {$this->label($parent)} {$parent->code}."
            );
        }
    }

    /** @param  class-string<Model>  $model */
    private function assertCodeFree(string $model, string $code, string $label): void
    {
        if ($model::withTrashed()->where('code', $code)->exists()) {
            $this->fail("Mã {$label} {$code} đã được dùng.", 'Mã không được cấp lại, kể cả khi đơn vị cũ đã xóa; hãy chọn mã khác.');
        }
    }

    private function label(Model $unit): string
    {
        return match (true) {
            $unit instanceof Faculty => 'khoa',
            $unit instanceof Department => 'bộ môn',
            $unit instanceof Major => 'ngành',
            $unit instanceof Specialization => 'chuyên ngành',
            default => 'hệ đào tạo',
        };
    }
}
