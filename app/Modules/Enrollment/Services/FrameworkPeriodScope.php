<?php

namespace App\Modules\Enrollment\Services;

use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Models\CurriculumSubject;
use App\Modules\Enrollment\Contracts\AcademicRecord;
use App\Modules\Enrollment\Enums\EnrollmentStatus;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\Faculty\Models\Major;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\TrainingFramework;
use App\Modules\SchoolClass\Services\TrainingFrameworkService;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Services\StudentCurriculumService;
use Illuminate\Support\Collection;

/**
 * Lớp sinh viên thấy trong đợt theo chương trình khung (cả học kỳ):
 *  (a) lớp của khung đúng khoa – khóa của sinh viên, môn thuộc CTĐT ngành của sinh viên (khung gom môn của mọi ngành
 *      trong khoa; môn chỉ có ở CTĐT ngành khác thì không hiện). Môn thêm tay ngoài CTĐT của mọi ngành trong khoa vẫn hiện
 *      cho cả khoa – khóa;
 *  (b) lớp "học bù": môn trong CTĐT của sinh viên ở kỳ trước kỳ của khung mình, chưa học hoặc học chưa đạt — lớp của bất
 *      kỳ khung nào trong đợt. Môn đang học ở học kỳ khác (đăng ký còn hiệu lực, chưa có kết quả) không tính là học bù:
 *      lập đợt cho học kỳ sau khi học kỳ hiện tại chưa xong thì môn kỳ trước của khóa vẫn đang học, chưa phải nợ.
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
        $studying = $this->studyingElsewhere($student, $period);
        $curriculum = $this->curricula->current($student);
        $plan = $curriculum === null ? collect() : CurriculumSubject::query()->where('curriculum_id', $curriculum->id)->pluck('planned_term', 'subject_id');
        $otherMajors = $plan->isEmpty() ? collect() : $this->otherMajorSubjects($framework, $curriculum->id)->diff($plan->keys());
        $result = [];

        $sections = CourseSection::query()->with('trainingFrameworkItem:id,training_framework_id')
            ->where('registration_period_id', $period->id)->where('status', '!=', SectionStatus::Cancelled)->get();

        foreach ($sections as $section) {
            if (in_array($section->subject_id, $passed, true)) {
                continue;
            }

            $own = $section->trainingFrameworkItem?->training_framework_id === $framework->id
                && ! $otherMajors->contains($section->subject_id);
            $plannedTerm = $plan->get($section->subject_id);
            $backlog = ! $own && $plannedTerm !== null && $myTerm !== null && (int) $plannedTerm < $myTerm
                && ! in_array($section->subject_id, $studying, true);

            if ($own || $backlog) {
                $result[$section->id] = ['backlog' => $backlog, 'planned_term' => $plannedTerm === null ? null : (int) $plannedTerm];
            }
        }

        return $result;
    }

    /**
     * Môn sinh viên đang học ở học kỳ khác: đăng ký còn hiệu lực chưa hoàn thành (đã đăng ký / đã xác nhận) và chưa có
     * kết quả. Môn đã học xong mà chưa đạt vẫn là học bù.
     *
     * @return list<int>
     */
    private function studyingElsewhere(Student $student, RegistrationPeriod $period): array
    {
        return Enrollment::query()->where('student_id', $student->id)->where('term_id', '!=', $period->term_id)
            ->whereIn('status', [EnrollmentStatus::Registered->value, EnrollmentStatus::Confirmed->value])
            ->pluck('subject_id')->diff($this->record->attemptedSubjectIds($student))->map(fn ($id) => (int) $id)->values()->all();
    }

    /** Môn trong CTĐT (đang áp dụng cho khóa) của các ngành khác cùng khoa với khung. */
    private function otherMajorSubjects(TrainingFramework $framework, int $ownCurriculumId): Collection
    {
        $curricula = Curriculum::query()->inForce()->forCohort($framework->cohort->start_year)
            ->whereIn('major_id', Major::query()->where('faculty_id', $framework->faculty_id)->select('id'))
            ->whereKeyNot($ownCurriculumId)->pluck('id');

        return CurriculumSubject::query()->whereIn('curriculum_id', $curricula)->distinct()->pluck('subject_id');
    }
}
