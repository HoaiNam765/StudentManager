<?php

namespace App\Modules\AcademicYear\Models;

use App\Models\User;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Đề nghị sửa mốc của học kỳ đã bắt đầu (BR-ACY-05): có lý do, chờ một người khác có quyền duyệt rồi mới áp dụng.
 * `changes` dạng {loại mốc: {old: 'Y-m-d'|null, new: 'Y-m-d'|null}}.
 */
class MilestoneChangeRequest extends StandardModel
{
    public const PENDING = 'pending';

    public const APPROVED = 'approved';

    public const REJECTED = 'rejected';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['changes' => 'array', 'decided_at' => 'datetime'];
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
