<?php

namespace App\Modules\Enrollment\Models;

use App\Models\User;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Auth\Contracts\HasDataScope;
use App\Modules\Auth\Enums\DataScope;
use App\Modules\Enrollment\Enums\EnrollmentStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Student\Models\Student;
use App\Modules\Subject\Models\Subject;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Một lần đăng ký lớp học phần của sinh viên (FR-ENR-003). Phạm vi: STU của mình (OWN), LEC lớp mình dạy (SECTION),
 * DEAN lớp thuộc khoa (FACULTY) — theo lớp học phần.
 */
class Enrollment extends StandardModel implements HasDataScope
{
    public const TYPES = ['normal' => 'Học lần đầu', 'retake' => 'Học lại', 'improve' => 'Học cải thiện'];

    protected $guarded = ['active_section_id'];

    protected function casts(): array
    {
        return [
            'status' => EnrollmentStatus::class,
            'registered_at' => 'immutable_datetime',
            'cancelled_at' => 'immutable_datetime',
            'withdrawn_at' => 'immutable_datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(RegistrationPeriod::class, 'registration_period_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn($this->qualifyColumn('status'), EnrollmentStatus::activeValues());
    }

    public function applyDataScope(Builder $query, DataScope $scope, User $user): void
    {
        if ($scope === DataScope::Own) {
            $query->whereIn($this->qualifyColumn('student_id'), Student::query()->select('id')->where('user_id', $user->id));

            return;
        }

        $query->whereIn(
            $this->qualifyColumn('course_section_id'),
            tap(CourseSection::query()->select('id'), fn (Builder $q) => (new CourseSection)->applyDataScope($q, $scope, $user)),
        );
    }
}
