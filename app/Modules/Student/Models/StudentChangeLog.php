<?php

namespace App\Modules\Student\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Lịch sử thay đổi hồ sơ sinh viên theo từng trường (FR-STU-005): giá trị trước – sau, người sửa, thời điểm, lý do.
 * Chỉ ghi thêm, không sửa. Giá trị số giấy tờ được che trước khi lưu (BR-STU-11).
 */
class StudentChangeLog extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'sensitive' => 'boolean',
            'changed_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
