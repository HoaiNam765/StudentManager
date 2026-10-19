<?php

namespace App\Modules\Student\Models;

use App\Models\User;
use App\Modules\Auth\Contracts\HasDataScope;
use App\Modules\Auth\Enums\DataScope;
use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\Specialization;
use App\Modules\Faculty\Models\TrainingType;
use App\Modules\Faculty\Services\FacultyAccess;
use App\Modules\SchoolClass\Models\AdminClassMember;
use App\Modules\Student\Enums\IdType;
use App\Modules\Student\Enums\StudentStatus;
use App\Modules\System\Models\AdministrativeUnit;
use App\Modules\System\Models\LookupValue;
use App\Support\Concerns\HasVietnameseSearch;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Hồ sơ sinh viên (FR-STU-001, 004). Số giấy tờ lưu mã hóa (`encrypted`), kiểm tra trùng qua `national_id_hash`.
 *
 * Phạm vi dữ liệu (BA 4.2, hàng STU): CTSV, ACAD toàn trường; DEAN sinh viên thuộc ngành của khoa mình (FACULTY);
 * STU hồ sơ của chính mình (OWN). LEC (SECTION, sinh viên lớp mình dạy) và ADV (ADVISEE, sinh viên thuộc lớp đang
 * cố vấn) được giới hạn qua các phân công hiệu lực của CLS/TCH.
 */
class Student extends StandardModel implements HasDataScope
{
    use HasVietnameseSearch;

    protected $guarded = [];

    protected array $searchable = ['student_code', 'full_name', 'school_email'];

    /** Không ghi nguyên văn vào nhật ký kiểm toán (BR-STU-11). Lịch sử thay đổi riêng ở student_change_logs. */
    protected array $auditMasked = ['national_id', 'national_id_hash', 'permanent_address', 'temporary_address'];

    protected $hidden = ['national_id', 'national_id_hash'];

    /** Trường nhạy cảm: cán bộ đổi phải ghi lý do (BR-STU-06). */
    public const SENSITIVE_FIELDS = ['full_name', 'date_of_birth', 'id_type', 'national_id', 'major_id', 'specialization_id'];

    protected function casts(): array
    {
        return [
            'national_id' => 'encrypted',
            'date_of_birth' => 'date',
            'national_id_issued_on' => 'date',
            'admission_date' => 'date',
            'cohort' => 'integer',
            'id_type' => IdType::class,
            'status' => StudentStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function gender(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'gender_id');
    }

    public function ethnicity(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'ethnicity_id');
    }

    public function religion(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'religion_id');
    }

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(LookupValue::class, 'nationality_id');
    }

    public function permanentUnit(): BelongsTo
    {
        return $this->belongsTo(AdministrativeUnit::class, 'permanent_unit_id');
    }

    public function temporaryUnit(): BelongsTo
    {
        return $this->belongsTo(AdministrativeUnit::class, 'temporary_unit_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(StudentContact::class);
    }

    public function adminClassMembers(): HasMany
    {
        return $this->hasMany(AdminClassMember::class);
    }

    public function changeLogs(): HasMany
    {
        return $this->hasMany(StudentChangeLog::class)->orderByDesc('changed_at')->orderByDesc('id');
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function scopeStatus(Builder $query, StudentStatus $status): Builder
    {
        return $query->where($this->qualifyColumn('status'), $status->value);
    }

    public function applyDataScope(Builder $query, DataScope $scope, User $user): void
    {
        match ($scope) {
            DataScope::Faculty => $query->whereIn(
                $this->qualifyColumn('major_id'),
                Major::query()->select('id')->whereIn('faculty_id', app(FacultyAccess::class)->facultyIds($user)),
            ),
            DataScope::Own => $query->where($this->qualifyColumn('user_id'), $user->id),
            DataScope::Advisee => $query->whereHas('adminClassMembers', fn (Builder $members) => $members
                ->current()
                ->whereHas('adminClass.advisorAssignments', fn (Builder $assignments) => $assignments
                    ->current()
                    ->whereHas('lecturer', fn (Builder $lecturers) => $lecturers->where('user_id', $user->id)))),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
