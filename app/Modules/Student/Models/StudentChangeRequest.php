<?php

namespace App\Modules\Student\Models;

use App\Models\User;
use App\Modules\Auth\Contracts\HasDataScope;
use App\Modules\Auth\Enums\DataScope;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Yêu cầu sửa trường nhạy cảm (BR-STU-06). Phạm vi dữ liệu theo hồ sơ sinh viên. */
class StudentChangeRequest extends StandardModel implements HasDataScope
{
    public const PENDING = 'pending';

    public const APPROVED = 'approved';

    public const REJECTED = 'rejected';

    public const CANCELLED = 'cancelled';

    public const STATUS_LABELS = [
        self::PENDING => 'Chờ duyệt',
        self::APPROVED => 'Đã duyệt',
        self::REJECTED => 'Từ chối',
        self::CANCELLED => 'Đã hủy',
    ];

    /** Không ghi giá trị đề nghị (có CCCD) vào nhật ký kiểm toán. */
    protected array $auditMasked = ['changes'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'changes' => 'encrypted:array',
            'decided_at' => 'immutable_datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function applyDataScope(Builder $query, DataScope $scope, User $user): void
    {
        $query->whereIn(
            $this->qualifyColumn('student_id'),
            tap(Student::query()->select('id'), fn (Builder $q) => (new Student)->applyDataScope($q, $scope, $user)),
        );
    }
}
