<?php

namespace App\Modules\SchoolClass\Models;

use App\Models\User;
use App\Modules\Auth\Contracts\HasDataScope;
use App\Modules\Auth\Enums\DataScope;
use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\Specialization;
use App\Modules\Faculty\Models\TrainingType;
use App\Modules\Faculty\Services\FacultyAccess;
use App\Support\Concerns\HasActiveStatus;
use App\Support\Concerns\HasVietnameseSearch;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Lớp hành chính (FR-CLS-002): đúng một khóa và một ngành (BR-CLS-01), chuyên ngành tùy chọn, hệ đào tạo, sĩ số tối đa.
 * Trạng thái hoạt động / đã đóng (ngừng hoạt động).
 *
 * Phạm vi dữ liệu (BA 4.2, hàng CLS): DEAN lớp thuộc ngành của khoa mình (FACULTY). ADV (lớp mình cố vấn), STU (lớp
 * của mình) có dữ liệu khi gán cố vấn và gán sinh viên vào lớp (#99).
 */
class AdminClass extends StandardModel implements HasDataScope
{
    use HasActiveStatus;
    use HasVietnameseSearch;

    protected $guarded = [];

    protected array $searchable = ['code', 'name'];

    protected function casts(): array
    {
        return ['max_size' => 'integer'];
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }

    public function trainingType(): BelongsTo
    {
        return $this->belongsTo(TrainingType::class);
    }

    public function applyDataScope(Builder $query, DataScope $scope, User $user): void
    {
        match ($scope) {
            DataScope::Faculty => $query->whereIn(
                $this->qualifyColumn('major_id'),
                Major::query()->select('id')->whereIn('faculty_id', app(FacultyAccess::class)->facultyIds($user)),
            ),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
