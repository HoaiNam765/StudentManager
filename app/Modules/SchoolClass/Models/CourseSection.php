<?php

namespace App\Modules\SchoolClass\Models;

use App\Models\User;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Auth\Contracts\HasDataScope;
use App\Modules\Auth\Enums\DataScope;
use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Services\FacultyAccess;
use App\Modules\Room\Models\Room;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\Subject\Models\Subject;
use App\Modules\Teacher\Models\Lecturer;
use App\Support\Concerns\HasVietnameseSearch;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Lớp học phần (FR-CLS-009..012). Không gắn với lớp hành chính (lớp ghép nhiều lớp, nhiều ngành).
 *
 * Phạm vi dữ liệu (BA 4.2, hàng CLS): DEAN lớp của học phần thuộc đơn vị mình quản lý (FACULTY); LEC lớp mình được
 * phân công (SECTION); STU lớp mình đăng ký (OWN, khi có ENR); ADV chưa áp dụng cho lớp học phần.
 */
class CourseSection extends StandardModel implements HasDataScope
{
    use HasVietnameseSearch;

    protected $guarded = [];

    protected array $searchable = ['code'];

    protected function casts(): array
    {
        return [
            'status' => SectionStatus::class,
            'sequence' => 'integer',
            'min_size' => 'integer',
            'max_size' => 'integer',
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function instructors(): HasMany
    {
        return $this->hasMany(SectionInstructor::class);
    }

    public function hasMainInstructor(): bool
    {
        return $this->instructors()->where('role', 'main')->exists();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn($this->qualifyColumn('status'), SectionStatus::activeValues());
    }

    public function applyDataScope(Builder $query, DataScope $scope, User $user): void
    {
        match ($scope) {
            DataScope::Faculty => $query->whereIn(
                $this->qualifyColumn('subject_id'),
                Subject::query()->select('id')->whereIn('department_id', $this->managedDepartments($user)),
            ),
            DataScope::Section => $query->whereHas('instructors', fn (Builder $q) => $q->whereIn(
                'lecturer_id',
                Lecturer::query()->select('id')->where('user_id', $user->id),
            )),
            default => $query->whereRaw('1 = 0'),
        };
    }

    /** @return list<int> bộ môn trưởng / phó đơn vị quản lý (cả bộ môn của khoa mình). */
    private function managedDepartments(User $user): array
    {
        $access = app(FacultyAccess::class);

        return array_values(array_unique([
            ...$access->departmentIds($user),
            ...Department::query()->whereIn('faculty_id', $access->facultyIds($user))->pluck('id')->all(),
        ]));
    }
}
