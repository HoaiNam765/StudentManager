<?php

namespace App\Modules\Curriculum\Models;

use App\Models\User;
use App\Modules\Auth\Contracts\HasDataScope;
use App\Modules\Auth\Enums\DataScope;
use App\Modules\Curriculum\Enums\CurriculumStatus;
use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\Specialization;
use App\Modules\Faculty\Models\TrainingType;
use App\Modules\Faculty\Services\FacultyAccess;
use App\Support\Concerns\HasVietnameseSearch;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

/**
 * Chương trình đào tạo (FR-CUR-001). Phạm vi dữ liệu (BA 4.2, hàng CUR): DEAN xây dựng CTĐT của ngành thuộc khoa mình
 * (FACULTY); STU chỉ xem CTĐT đang / đã gán cho mình (OWN, `student_curricula`, #97); các vai trò khác xem toàn trường.
 */
class Curriculum extends StandardModel implements HasDataScope
{
    use HasVietnameseSearch;

    protected $table = 'curricula';

    protected $guarded = [];

    protected array $searchable = ['code', 'name'];

    protected function casts(): array
    {
        return [
            'status' => CurriculumStatus::class,
            'published_at' => 'datetime',
            'effective_from' => 'date',
            'version' => 'integer',
            'cohort_from' => 'integer',
            'cohort_to' => 'integer',
            'total_credits' => 'integer',
            'standard_terms' => 'integer',
        ];
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

    public function blocks(): HasMany
    {
        return $this->hasMany(CurriculumBlock::class)->orderBy('sort_order')->orderBy('id');
    }

    public function electiveGroups(): HasMany
    {
        return $this->hasMany(ElectiveGroup::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CurriculumSubject::class);
    }

    public function graduationRequirement(): HasOne
    {
        return $this->hasOne(GraduationRequirement::class);
    }

    public function basedOn(): BelongsTo
    {
        return $this->belongsTo(self::class, 'based_on_id');
    }

    public function isDraft(): bool
    {
        return $this->status === CurriculumStatus::Draft;
    }

    /** CTĐT đang áp dụng: đã ban hành, chưa ngừng (BR-CUR-07). */
    public function scopeInForce(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('status'), CurriculumStatus::Published->value);
    }

    /** CTĐT áp dụng cho khóa $cohort. */
    public function scopeForCohort(Builder $query, int $cohort): Builder
    {
        return $query->where('cohort_from', '<=', $cohort)
            ->where(fn (Builder $q) => $q->whereNull('cohort_to')->orWhere('cohort_to', '>=', $cohort));
    }

    public function applyDataScope(Builder $query, DataScope $scope, User $user): void
    {
        match ($scope) {
            DataScope::Faculty => $query->whereIn('major_id', Major::query()->select('id')->whereIn('faculty_id', app(FacultyAccess::class)->facultyIds($user))),
            DataScope::Own => $query->whereIn($this->qualifyColumn('id'), DB::table('student_curricula')
                ->join('students', 'students.id', '=', 'student_curricula.student_id')
                ->where('students.user_id', $user->id)
                ->whereNull('student_curricula.deleted_at')
                ->select('student_curricula.curriculum_id')),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
