<?php

namespace App\Modules\Subject\Models;

use App\Models\User;
use App\Modules\Auth\Contracts\HasDataScope;
use App\Modules\Auth\Enums\DataScope;
use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Services\FacultyAccess;
use App\Modules\Subject\Contracts\SubjectAssignments;
use App\Modules\Subject\Enums\KnowledgeBlock;
use App\Modules\Subject\Enums\SubjectRequirement;
use App\Modules\Subject\Enums\TeachingLanguage;
use App\Support\Concerns\HasActiveStatus;
use App\Support\Concerns\HasVietnameseSearch;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Học phần trong danh mục dùng chung (FR-SUB-001..003).
 *
 * Phạm vi dữ liệu (BA 4.2, hàng SUB): DEAN quản lý học phần của các bộ môn mình phụ trách (FACULTY);
 * LEC chỉ thấy, chỉ sửa (đề cương) học phần mình phụ trách (SECTION, qua `SubjectAssignments`: tạm thời là học phần
 * của bộ môn giảng viên, CLS #107 đổi thành học phần của lớp được phân công). Các vai trò còn lại xem toàn trường.
 */
class Subject extends StandardModel implements HasDataScope
{
    use HasActiveStatus;
    use HasVietnameseSearch;

    protected $guarded = [];

    protected array $searchable = ['code', 'name', 'name_en'];

    protected function casts(): array
    {
        return [
            'teaching_language' => TeachingLanguage::class,
            'requirement' => SubjectRequirement::class,
            'knowledge_block' => KnowledgeBlock::class,
            'counts_toward_gpa' => 'boolean',
            'is_graduation_requirement' => 'boolean',
            'credits' => 'integer',
            'theory_credits' => 'integer',
            'practice_credits' => 'integer',
            'theory_periods' => 'integer',
            'practice_periods' => 'integer',
            'self_study_hours' => 'integer',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Học phần điều kiện (không tính GPA, ví dụ giáo dục thể chất): vẫn phải Đạt nếu là điều kiện tốt nghiệp
     * của CTĐT (BR-SUB-06). GRD chấm Đạt / Không đạt, GRA kiểm tra khi xét tốt nghiệp.
     */
    public function isPassFailOnly(): bool
    {
        return ! $this->counts_toward_gpa;
    }

    public function getKnowledgeBlockLabelAttribute(): string
    {
        return $this->knowledge_block->label();
    }

    public function getRequirementLabelAttribute(): string
    {
        return $this->requirement->label();
    }

    public function getCountsTowardGpaLabelAttribute(): string
    {
        return $this->counts_toward_gpa ? 'Có' : 'Không';
    }

    public function getGraduationRequirementLabelAttribute(): string
    {
        return $this->is_graduation_requirement ? 'Có' : 'Không';
    }

    public function getTeachingLanguageLabelAttribute(): string
    {
        return $this->teaching_language->label();
    }

    public function applyDataScope(Builder $query, DataScope $scope, User $user): void
    {
        match ($scope) {
            DataScope::Faculty => $query->whereIn('department_id', app(FacultyAccess::class)->departmentIds($user)),
            DataScope::Section => $query->whereIn($this->qualifyColumn('id'), app(SubjectAssignments::class)->subjectIdsFor($user)),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
