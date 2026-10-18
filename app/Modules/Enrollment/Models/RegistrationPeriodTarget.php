<?php

namespace App\Modules\Enrollment\Models;

use App\Modules\Faculty\Models\Major;
use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\SchoolClass\Models\Cohort;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Một tổ hợp đối tượng của đợt đăng ký: khóa / ngành / lớp hành chính; cột trống = không giới hạn. */
class RegistrationPeriodTarget extends Model
{
    protected $guarded = [];

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function adminClass(): BelongsTo
    {
        return $this->belongsTo(AdminClass::class);
    }

    public function describe(): string
    {
        $parts = array_filter([
            $this->cohort ? "khóa {$this->cohort->code}" : null,
            $this->major ? "ngành {$this->major->name}" : null,
            $this->adminClass ? "lớp {$this->adminClass->code}" : null,
        ]);

        return $parts === [] ? 'mọi sinh viên' : implode(', ', $parts);
    }
}
