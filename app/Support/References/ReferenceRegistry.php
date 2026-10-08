<?php

namespace App\Support\References;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Sổ đăng ký "ai đang tham chiếu tới bản ghi này", dùng cho các quy tắc:
 *   - danh mục đã được tham chiếu thì chỉ ngừng sử dụng, không xóa (BR-SYS-09, GC-02, GC-05);
 *   - không đổi mã khi đã có dữ liệu liên quan (BR-FAC-01);
 *   - không xóa phòng đã có lịch sử sử dụng (BR-ROM-04).
 *
 * Module nào thêm khóa ngoại tới một danh mục thì đăng ký trong ServiceProvider của module đó,
 * nên module danh mục không cần biết trước ai sẽ dùng mình:
 *
 *     app(ReferenceRegistry::class)->register(LookupValue::class, 'students', 'gender_id', 'sinh viên');
 *
 * Bảng chưa tồn tại (module chưa cài) được bỏ qua. Bản ghi đã xóa mềm vẫn được tính là đang tham chiếu,
 * vì dữ liệu đã phát sinh không được mất liên kết.
 */
final class ReferenceRegistry
{
    /** @var array<class-string<Model>, list<array{table: string, column: string, label: string, constraint: ?Closure, active: ?Closure}>> */
    private array $references = [];

    /**
     * @param  class-string<Model>  $model  Model bị tham chiếu
     * @param  string  $label  Tên hiển thị của dữ liệu tham chiếu, ví dụ "bộ môn", "sinh viên"
     * @param  (Closure(Builder): void)|null  $constraint  Điều kiện thêm, ví dụ chỉ tính bản ghi cùng loại
     * @param  (Closure(Builder): void)|null  $active  Điều kiện "còn hoạt động" (ví dụ sinh viên đang học); khai báo thì
     *                                                 tham chiếu này còn chặn việc ngừng hoạt động bản ghi bị tham chiếu
     *                                                 cho tới khi chuyển hết (BR-FAC-05), xem `activeUsages()`
     */
    public function register(string $model, string $table, string $column, string $label, ?Closure $constraint = null, ?Closure $active = null): void
    {
        $this->references[$model][] = compact('table', 'column', 'label', 'constraint', 'active');
    }

    /**
     * Chỉ tính các tham chiếu còn hoạt động (đã khai báo `$active` khi đăng ký): dữ liệu phải chuyển đi
     * trước khi được ngừng hoạt động bản ghi bị tham chiếu.
     *
     * @return array<string, int>
     */
    public function activeUsages(Model $model): array
    {
        $usages = [];

        foreach ($this->references[$model::class] ?? [] as $reference) {
            if ($reference['active'] === null || ! Schema::hasTable($reference['table'])) {
                continue;
            }

            $query = DB::table($reference['table'])->where($reference['column'], $model->getKey());

            if ($reference['constraint'] !== null) {
                ($reference['constraint'])($query);
            }

            ($reference['active'])($query);
            $count = $query->count();

            if ($count > 0) {
                $usages[$reference['label']] = ($usages[$reference['label']] ?? 0) + $count;
            }
        }

        return $usages;
    }

    /**
     * Số bản ghi đang tham chiếu tới $model, theo từng loại dữ liệu.
     *
     * @return array<string, int> ['bộ môn' => 3, 'ngành' => 2]
     */
    public function usages(Model $model): array
    {
        $usages = [];

        foreach ($this->references[$model::class] ?? [] as $reference) {
            if (! Schema::hasTable($reference['table'])) {
                continue;
            }

            $query = DB::table($reference['table'])->where($reference['column'], $model->getKey());

            if ($reference['constraint'] !== null) {
                ($reference['constraint'])($query);
            }

            $count = $query->count();

            if ($count > 0) {
                $usages[$reference['label']] = ($usages[$reference['label']] ?? 0) + $count;
            }
        }

        return $usages;
    }

    public function isReferenced(Model $model): bool
    {
        return $this->usages($model) !== [];
    }

    /** "3 bộ môn, 2 ngành" để đưa vào thông báo lỗi. */
    public static function describe(array $usages): string
    {
        return implode(', ', array_map(
            fn (string $label, int $count) => "{$count} {$label}",
            array_keys($usages),
            $usages,
        ));
    }
}
