<?php

namespace App\Modules\SchoolClass\Models;

use App\Modules\Subject\Models\Subject;
use App\Support\Models\StandardModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Một học phần trong chương trình khung: bắt buộc / tự chọn, số lớp dự kiến, sĩ số tối đa mỗi lớp. */
class TrainingFrameworkItem extends StandardModel
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'compulsory' => 'boolean',
            'planned_sections' => 'integer',
            'max_size' => 'integer',
        ];
    }

    public function framework(): BelongsTo
    {
        return $this->belongsTo(TrainingFramework::class, 'training_framework_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /** Lớp học phần đã mở từ mục này (kể cả đã hủy; đếm lớp còn hoạt động dùng `scopeActive`). */
    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class);
    }
}
