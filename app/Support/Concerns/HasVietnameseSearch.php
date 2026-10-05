<?php

namespace App\Support\Concerns;

use App\Support\Text\Vietnamese;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Tìm kiếm không phân biệt hoa thường và dấu tiếng Việt (GC-01, NFR-LOC-02),
 * sắp xếp theo thứ tự chữ cái tiếng Việt.
 *
 * Cách dùng: model khai báo các cột cần tìm
 *     protected array $searchable = ['code', 'name'];
 * Trait tự lưu bản đã bỏ dấu vào cột `search_text` mỗi lần lưu bằng Eloquent
 * (`$table->searchText()` trong migration). Ghi hàng loạt bằng query builder
 * hoặc insert thô sẽ không cập nhật cột này.
 *
 *     Faculty::query()->search('cong nghe')->orderByVietnamese('name')->get();
 *
 * Vì sao không chỉ dựa vào collation: `utf8mb4_unicode_ci` và `utf8mb4_vietnamese_ci`
 * đều coi `d` khác `đ`, nên tìm "dang" sẽ không ra "Đặng".
 */
trait HasVietnameseSearch
{
    public static function bootHasVietnameseSearch(): void
    {
        static::saving(function (Model $model): void {
            $model->search_text = $model->buildSearchText();
        });
    }

    public function buildSearchText(): string
    {
        $parts = array_map(
            fn (string $column): string => (string) $this->getAttribute($column),
            $this->searchable ?? []
        );

        return Vietnamese::fold(implode(' ', $parts));
    }

    /** Mọi từ trong $term phải xuất hiện (theo bất kỳ thứ tự nào). */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $folded = Vietnamese::fold($term);

        if ($folded === '') {
            return $query;
        }

        $column = $query->getQuery()->getGrammar()->wrap($this->qualifyColumn('search_text'));

        foreach (explode(' ', $folded) as $word) {
            $query->whereRaw("{$column} like ? escape '!'", ['%'.Vietnamese::escapeLike($word).'%']);
        }

        return $query;
    }

    /** Sắp xếp theo thứ tự chữ cái tiếng Việt (Anh, Ân, Ba, Dũng, Đạt…). */
    public function scopeOrderByVietnamese(Builder $query, string $column, string $direction = 'asc'): Builder
    {
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        if ($query->getQuery()->getConnection()->getDriverName() !== 'mysql') {
            return $query->orderBy($column, $direction);
        }

        $wrapped = $query->getQuery()->getGrammar()->wrap($this->qualifyColumn($column));

        return $query->orderByRaw("{$wrapped} collate utf8mb4_vietnamese_ci {$direction}");
    }
}
