<?php

namespace App\Modules\Curriculum;

use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Models\CurriculumBlock;
use App\Modules\Curriculum\Models\CurriculumSubject;
use App\Modules\Curriculum\Models\ElectiveGroup;
use App\Modules\Curriculum\Policies\CurriculumPolicy;
use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Models\Specialization;
use App\Modules\Faculty\Models\TrainingType;
use App\Modules\Subject\Models\Subject;
use App\Support\References\ReferenceRegistry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Đăng ký của module Curriculum (CUR).
 *
 * - Học phần nằm trong CTĐT đã ban hành thì không đổi mã, tín chỉ, không xóa (BR-SUB-01, 05); CTĐT đang áp dụng thì
 *   học phần chưa ngừng được. CTĐT nháp không khóa học phần.
 * - Ngành, chuyên ngành, hệ đào tạo còn CTĐT đang áp dụng thì không ngừng được (BR-FAC-05).
 */
class CurriculumServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        foreach ([Curriculum::class, CurriculumBlock::class, ElectiveGroup::class, CurriculumSubject::class] as $model) {
            Gate::policy($model, CurriculumPolicy::class);
        }

        $references = app(ReferenceRegistry::class);
        $published = fn ($q) => $q->whereIn('curriculum_id', DB::table('curricula')->select('id')->whereIn('status', ['published', 'retired']));
        $inForce = fn ($q) => $q->whereIn('curriculum_id', DB::table('curricula')->select('id')->where('status', 'published')->whereNull('deleted_at'));
        $curriculumInForce = fn ($q) => $q->where('status', 'published')->whereNull('deleted_at');

        $references->register(Subject::class, 'curriculum_subjects', 'subject_id', 'CTĐT đã ban hành', constraint: $published, active: $inForce);
        $references->register(Major::class, 'curricula', 'major_id', 'CTĐT', active: $curriculumInForce);
        $references->register(Specialization::class, 'curricula', 'specialization_id', 'CTĐT', active: $curriculumInForce);
        $references->register(TrainingType::class, 'curricula', 'training_type_id', 'CTĐT', active: $curriculumInForce);
    }
}
