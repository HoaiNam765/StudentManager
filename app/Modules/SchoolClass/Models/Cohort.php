<?php

namespace App\Modules\SchoolClass\Models;

use App\Modules\System\Models\PolicySet;
use App\Modules\System\Services\PolicyResolver;
use App\Support\Concerns\HasActiveStatus;
use App\Support\Concerns\HasVietnameseSearch;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Khóa tuyển sinh (FR-CLS-001), mã kiểu HUIT: số khóa + DH, ví dụ 16DH = nhập học 2026 (`codeFor`). Năm bắt đầu (`start_year`) là "khóa" dùng ở STU, CUR, SYS (`cohort`).
 * Bộ quy chế: gắn cố định ở `policy_set_id`, hoặc để trống thì theo `PolicyResolver` cho năm bắt đầu.
 * Danh mục dùng chung toàn trường: mọi vai trò có quyền xem CLS đều xem được.
 */
class Cohort extends StandardModel
{
    use HasActiveStatus;
    use HasVietnameseSearch;

    protected $guarded = [];

    protected array $searchable = ['code', 'name'];

    /** Số khóa của năm nhập học: 2024 → 14, 2025 → 15, 2026 → 16. */
    public const NUMBER_OFFSET = 2010;

    /** Mã khóa mặc định của năm nhập học: 2026 → "16DH". */
    public static function codeFor(int $startYear): string
    {
        return ($startYear - self::NUMBER_OFFSET).'DH';
    }

    protected function casts(): array
    {
        return [
            'start_year' => 'integer',
            'expected_end_year' => 'integer',
        ];
    }

    public function policySet(): BelongsTo
    {
        return $this->belongsTo(PolicySet::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(AdminClass::class);
    }

    /** Bộ quy chế áp dụng: gắn cố định, hoặc bộ đã ban hành áp dụng cho năm bắt đầu của khóa. */
    public function effectivePolicySet(): PolicySet
    {
        return $this->policySet ?? app(PolicyResolver::class)->setFor($this->start_year);
    }
}
