<?php

namespace App\Modules\Enrollment\Services;

use App\Modules\Curriculum\Models\CurriculumSubject;
use App\Modules\Enrollment\Contracts\AcademicRecord;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Services\TrainingFrameworkService;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Services\StudentCurriculumService;

/**
 * Lớp sinh viên thấy trong đợt theo chương trình khung (cả học kỳ):
 *  (a) lớp của khung đúng khoa – khóa của sinh viên;
 *  (b) lớp "học bù": môn trong CTĐT của sinh viên ở kỳ trước kỳ của khung mình, chưa học hoặc học chưa đạt — lớp của bất
 *      kỳ khung nào trong đợt.
 * Môn đã đạt không hiện. Dùng chung cho danh sách lớp (AvailableSections) và quy tắc đăng ký (EnrollmentRules).
 */
class FrameworkPeriodScope
{
    public function __construct(
        private readonly StudentCurriculumService $curricula,
        private readonly AcademicRecord $record,
        private readonly TrainingFrameworkService $frameworks,
    ) {}

    /**
     * @return array<int, array{backlog: bool, planned_term: ?int}> mã lớp => học bù?, kỳ CTĐT của môn
     */
    public function sectionsFor(Student $student, RegistrationPeriod $period): array
    {
        $period->loadMissing('frameworks.cohort');
        $framework = $period->frameworkFor($student);

        if ($framework === null) {
            return [];
        }

        $myTerm = $this->frameworks->plannedTermOf($framework);
        $passed = $this->record->passedSubjectIds($student);
        $curriculum = $this->curricula->current($student);
        $plan = $curriculum === null ? collect() : CurriculumSubject::query()->where('curriculum_id', $curriculum->id)->pluck('planned_term', 'subject_id');
        $result = [];

        $sections = CourseSection::query()->with('trainingFrameworkItem:id,training_framework_id')
            ->where('registration_period_id', $period->id)->where('status', '!=', SectionStatus::Cancelled)->get();

        foreach ($sections as $section) {
            if (in_array($section->subject_id, $passed, true)) {
                continue;
            }

            $own = $section->trainingFrameworkItem?->training_framework_id === $framework->id;
            $plannedTerm = $plan->get($section->subject_id);
            $backlog = ! $own && $plannedTerm !== null && $myTerm !== null && (int) $plannedTerm < $myTerm;

            if ($own || $backlog) {
                $result[$section->id] = ['backlog' => $backlog, 'planned_term' => $plannedTerm === null ? null : (int) $plannedTerm];
            }
        }

        return $result;
    }
}
