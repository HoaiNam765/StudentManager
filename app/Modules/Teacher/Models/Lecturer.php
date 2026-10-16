<?php

namespace App\Modules\Teacher\Models;

use App\Models\User;
use App\Modules\Auth\Contracts\HasDataScope;
use App\Modules\Auth\Enums\DataScope;
use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Services\FacultyAccess;
use App\Modules\System\Models\LookupValue;
use App\Modules\Teacher\Enums\AcademicDegree;
use App\Modules\Teacher\Enums\AcademicTitle;
use App\Modules\Teacher\Enums\LecturerStatus;
use App\Support\Concerns\HasVietnameseSearch;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Hồ sơ giảng viên (FR-TCH-001). Đơn vị chính là một bộ môn (BR-FAC-03), kiêm nhiệm ở `units()`.
 *
 * Phạm vi dữ liệu (BA 4.2, hàng TCH): DEAN quản lý giảng viên có đơn vị chính hoặc đơn vị kiêm nhiệm đang hiệu lực
 * thuộc đơn vị mình (FACULTY); LEC, ADV chỉ hồ sơ của chính mình (OWN); các vai trò khác xem toàn trường
 * (thông tin nhạy cảm do controller che).
 */
class Lecturer extends StandardModel implements HasDataScope
{
    use HasVietnameseSearch;

    protected $guarded = [];

    protected array $searchable = ['code', 'full_name', 'email'];

    /** CCCD không ghi nguyên văn vào nhật ký kiểm toán. */
    protected array $auditMasked = ['national_id'];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'hired_on' => 'date',
            'left_on' => 'date',
            'academic_title' => AcademicTitle::class,
            'academic_degree' => AcademicDegree::class,
            'status' => LecturerStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function gender(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'gender_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(LecturerUnit::class);
    }

    public function changeRequests(): HasMany
    {
        return $this->hasMany(LecturerChangeRequest::class);
    }

    /** Được phân công giảng dạy, cố vấn mới không (BR-TCH-02, BR-TCH-09). */
    public function isAssignable(): bool
    {
        return $this->status->isActive();
    }

    public function scopeWorking(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('status'), LecturerStatus::Working->value);
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getAcademicTitleLabelAttribute(): string
    {
        return $this->academic_title->label();
    }

    public function getAcademicDegreeLabelAttribute(): ?string
    {
        return $this->academic_degree?->label();
    }

    public function applyDataScope(Builder $query, DataScope $scope, User $user): void
    {
        match ($scope) {
            DataScope::Faculty => $this->scopeToManagedUnits($query, $user),
            DataScope::Own => $query->where($this->qualifyColumn('user_id'), $user->id),
            default => $query->whereRaw('1 = 0'),
        };
    }

    private function scopeToManagedUnits(Builder $query, User $user): void
    {
        $access = app(FacultyAccess::class);
        $departments = $access->departmentIds($user);
        $faculties = $access->facultyIds($user);
        $today = now(config('studentmanager.display_timezone'))->toDateString();

        $query->where(fn (Builder $q) => $q
            ->whereIn($this->qualifyColumn('department_id'), $departments)
            ->orWhereHas('units', fn (Builder $u) => $u
                ->effectiveOn($today)
                ->where(fn (Builder $w) => $w
                    ->where(fn (Builder $d) => $d->where('unit_type', 'department')->whereIn('unit_id', $departments))
                    ->orWhere(fn (Builder $f) => $f->where('unit_type', 'faculty')->whereIn('unit_id', $faculties)))));
    }
}
