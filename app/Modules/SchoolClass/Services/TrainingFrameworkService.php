<?php

namespace App\Modules\SchoolClass\Services;

use App\Models\User;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\AcademicYear\Models\TermStatus;
use App\Modules\AcademicYear\Models\TermType;
use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Models\CurriculumSubject;
use App\Modules\Enrollment\Enums\PeriodStatus;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\Faculty\Models\Major;
use App\Modules\Notification\Models\Notification;
use App\Modules\Notification\Services\AnnouncementService;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\Cohort;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\TrainingFramework;
use App\Modules\SchoolClass\Models\TrainingFrameworkItem;
use App\Modules\Student\Enums\StudentStatus;
use App\Modules\Student\Models\Student;
use App\Modules\Subject\Models\Subject;
use App\Support\Exceptions\BusinessRuleException;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Chương trình khung theo khoa – khóa – học kỳ: khai báo học phần dự kiến mở, gợi ý từ CTĐT, mở lớp học phần hàng loạt
 * và thông báo cho sinh viên của khoa – khóa đó.
 *
 * - Mỗi (khoa, khóa, học kỳ) một khung. Học kỳ đã kết thúc / khóa thì không sửa, không mở lớp.
 * - Gợi ý từ CTĐT: học phần có "học kỳ dự kiến" bằng học kỳ thứ n của khóa (tính theo năm học: năm thứ k, HK1 / HK2)
 *   trong các CTĐT đã ban hành của ngành thuộc khoa, áp dụng cho khóa. Chỉ thêm học phần chưa có trong khung.
 * - Mở lớp: mỗi mục mở thêm (số lớp dự kiến − số lớp đang hoạt động đã mở từ mục) qua `CourseSectionService::open`
 *   (giữ mọi kiểm tra BR-CLS-02, 03, BR-SUB-08). Mục lỗi không chặn mục khác; chạy lại không mở trùng.
 * - Mục đã có lớp: không xóa, không giảm số lớp dự kiến dưới số lớp đã mở. Khung đã có lớp: không xóa.
 * - Thông báo: soạn sẵn nội dung (học phần, lớp, ngày bắt đầu học kỳ, thời gian đăng ký) rồi gửi qua AnnouncementService
 *   cho sinh viên thuộc khoa **và** khóa (BR-NOT-05 do AudienceResolver kiểm tra).
 */
class TrainingFrameworkService extends BaseService
{
    public function __construct(
        private readonly CourseSectionService $sections,
        private readonly AnnouncementService $announcements,
    ) {}

    /** @param  array{faculty_id: int, cohort_id: int, term_id: int, note?: ?string}  $data */
    public function create(array $data): TrainingFramework
    {
        $term = Term::query()->findOrFail($data['term_id']);
        $this->assertTermOpen($term);

        $exists = TrainingFramework::query()
            ->where(['faculty_id' => $data['faculty_id'], 'cohort_id' => $data['cohort_id'], 'term_id' => $term->id])
            ->exists();

        if ($exists) {
            $this->fail('Khoa – khóa – học kỳ này đã có chương trình khung.', 'Mở chương trình khung đã có để sửa thay vì tạo mới.');
        }

        return TrainingFramework::create([
            'faculty_id' => $data['faculty_id'],
            'cohort_id' => $data['cohort_id'],
            'term_id' => $term->id,
            'note' => $data['note'] ?? null,
        ]);
    }

    public function update(TrainingFramework $framework, array $data): TrainingFramework
    {
        $this->assertTermOpen($framework->term);
        $framework->update(['note' => $data['note'] ?? null]);

        return $framework->refresh();
    }

    public function delete(TrainingFramework $framework): void
    {
        if (DB::table('registration_period_frameworks')->where('training_framework_id', $framework->id)->exists()) {
            $this->fail('Chương trình khung đang được tham chiếu bởi đợt đăng ký.', 'Sửa học phần trong khung hoặc xóa đợt nháp chưa có lớp trước khi xóa khung.');
        }
        $opened = CourseSection::query()->whereIn('training_framework_item_id', $framework->items()->select('id'))->count();

        if ($opened > 0) {
            $this->fail("Chương trình khung đã mở {$opened} lớp học phần nên không xóa được.", 'Hủy / xóa các lớp đã mở trước, hoặc giữ khung để theo dõi.');
        }

        $this->transaction(function () use ($framework): void {
            $framework->items()->get()->each->forceDelete();
            $framework->forceDelete();
        });
    }

    /** @param  array{subject_id: int, compulsory?: bool, planned_sections?: int, max_size?: ?int, note?: ?string}  $data */
    public function addItem(TrainingFramework $framework, array $data): TrainingFrameworkItem
    {
        $this->assertTermOpen($framework->term);
        $subject = Subject::query()->findOrFail($data['subject_id']);

        if (! $subject->isActive()) {
            $this->fail("Học phần {$subject->code} đang ngừng hoạt động.", 'Chọn học phần đang hoạt động.');
        }

        if ($framework->items()->where('subject_id', $subject->id)->exists()) {
            $this->fail("Học phần {$subject->code} đã có trong chương trình khung.", 'Sửa số lớp của dòng đã có thay vì thêm lại.');
        }

        $this->assertMaxSize($data['max_size'] ?? null);

        return $framework->items()->create([
            'subject_id' => $subject->id,
            'compulsory' => (bool) ($data['compulsory'] ?? true),
            'planned_sections' => max(1, (int) ($data['planned_sections'] ?? 1)),
            'max_size' => $data['max_size'] ?? null,
            'note' => $data['note'] ?? null,
        ]);
    }

    public function updateItem(TrainingFrameworkItem $item, array $data): TrainingFrameworkItem
    {
        $this->assertTermOpen($item->framework->term);
        $opened = $this->openedCount($item);

        if (isset($data['planned_sections']) && (int) $data['planned_sections'] < $opened) {
            $this->fail(
                "Học phần đã mở {$opened} lớp, không giảm số lớp dự kiến xuống {$data['planned_sections']} được.",
                'Hủy bớt lớp đã mở trước, hoặc giữ số lớp dự kiến ≥ số lớp đã mở.'
            );
        }

        if (array_key_exists('max_size', $data)) {
            $this->assertMaxSize($data['max_size']);
        }

        $item->update(array_intersect_key($data, array_flip(['compulsory', 'planned_sections', 'max_size', 'note'])));

        return $item->refresh();
    }

    public function deleteItem(TrainingFrameworkItem $item): void
    {
        $this->assertTermOpen($item->framework->term);

        if ($item->sections()->whereNull('registration_period_id')->exists()) {
            $this->fail('Học phần này đã mở lớp từ chương trình khung nên không xóa được.', 'Hủy / xóa các lớp đã mở trước, hoặc giảm số lớp dự kiến.');
        }

        $item->forceDelete();
    }

    /**
     * Gợi ý học phần từ CTĐT đã ban hành (đúng học kỳ thứ n của khóa). Không ghi đè dòng đã có.
     *
     * @return array{planned_term: ?int, students: int, added: list<TrainingFrameworkItem>, message: ?string}
     */
    public function suggestFromCurriculum(TrainingFramework $framework): array
    {
        $framework->loadMissing(['cohort', 'term.academicYear']);
        $this->assertTermOpen($framework->term);
        $plannedTerm = $this->plannedTermOf($framework);
        $students = $this->studentCount($framework);

        if ($plannedTerm === null) {
            return ['planned_term' => null, 'students' => $students, 'added' => [], 'message' => 'Học kỳ hè / học kỳ trước khi khóa nhập học không có học phần theo CTĐT; thêm học phần thủ công.'];
        }

        $majorIds = Major::query()->where('faculty_id', $framework->faculty_id)->pluck('id');
        $curricula = Curriculum::query()->inForce()->forCohort($framework->cohort->start_year)->whereIn('major_id', $majorIds)->pluck('id');
        $rows = CurriculumSubject::query()->whereIn('curriculum_id', $curricula)->where('planned_term', $plannedTerm)->get()->groupBy('subject_id');
        $existing = $framework->items()->pluck('subject_id')->all();
        $perClass = (int) config('studentmanager.course_section.default_max_size', 60);
        $added = [];

        foreach ($rows as $subjectId => $items) {
            if (in_array($subjectId, $existing, true) || ! Subject::query()->find($subjectId)?->isActive()) {
                continue;
            }

            $compulsory = $items->contains(fn (CurriculumSubject $c) => $c->curriculum_elective_group_id === null);
            $added[] = $framework->items()->create([
                'subject_id' => $subjectId,
                'compulsory' => $compulsory,
                'planned_sections' => $compulsory ? max(1, (int) ceil($students / max(1, $perClass))) : 1,
            ]);
        }

        return [
            'planned_term' => $plannedTerm,
            'students' => $students,
            'added' => $added,
            'message' => $rows->isEmpty() ? "CTĐT của các ngành thuộc khoa không có học phần nào ở học kỳ {$plannedTerm} cho khóa {$framework->cohort->code}." : null,
        ];
    }

    /**
     * Mở lớp học phần còn thiếu cho các mục (mặc định mọi mục). Mục lỗi được báo riêng, không chặn mục khác.
     *
     * @param  list<int>|null  $itemIds
     * @return array{opened: list<CourseSection>, errors: list<array{item_id: int, subject: string, message: string, hint: ?string}>}
     */
    public function openSections(TrainingFramework $framework, ?array $itemIds = null): array
    {
        $this->assertTermOpen($framework->term);
        $items = $framework->items()->with('subject')->when($itemIds !== null, fn ($q) => $q->whereIn('id', $itemIds))->get();
        $defaultMin = (int) config('studentmanager.course_section.default_min_size', 15);
        $opened = [];
        $errors = [];

        foreach ($items as $item) {
            $missing = $item->planned_sections - $this->openedCount($item);

            for ($i = 0; $i < $missing; $i++) {
                try {
                    $data = ['subject_id' => $item->subject_id, 'term_id' => $framework->term_id, 'training_framework_item_id' => $item->id];

                    if ($item->max_size !== null) {
                        $data['max_size'] = $item->max_size;
                        $data['min_size'] = min($defaultMin, $item->max_size);
                    }

                    $opened[] = $this->sections->open($data);
                } catch (BusinessRuleException $e) {
                    $errors[] = ['item_id' => $item->id, 'subject' => "{$item->subject->code} – {$item->subject->name}", 'message' => $e->getMessage(), 'hint' => $e->hint()];

                    break;
                }
            }
        }

        return ['opened' => $opened, 'errors' => $errors];
    }

    /** @return array{title: string, body: string, students: int} */
    public function draftAnnouncement(TrainingFramework $framework): array
    {
        $framework->loadMissing(['faculty', 'cohort', 'term.academicYear', 'items.subject']);
        $term = $framework->term;
        $termLabel = trim($term->name.($term->academicYear ? " năm học {$term->academicYear->name}" : ''));
        $sections = CourseSection::query()
            ->whereIn('training_framework_item_id', $framework->items->pluck('id'))
            ->where('status', '!=', SectionStatus::Cancelled->value)
            ->orderBy('code')->get()->groupBy('training_framework_item_id');

        $lines = [
            "Phòng Đào tạo thông báo các học phần mở trong {$termLabel} cho sinh viên khóa {$framework->cohort->code} {$framework->faculty->name}:",
            '',
        ];

        foreach ($framework->items->sortBy(fn (TrainingFrameworkItem $i) => [! $i->compulsory, $i->subject->code]) as $item) {
            $codes = ($sections->get($item->id) ?? collect())->pluck('code')->implode(', ');
            $lines[] = "- {$item->subject->code} {$item->subject->name} ({$item->subject->credits} tín chỉ, ".($item->compulsory ? 'bắt buộc' : 'tự chọn').')'
                .($codes !== '' ? ": lớp {$codes}" : ': lớp sẽ mở sau');
        }

        $lines[] = '';
        $lines[] = 'Học kỳ bắt đầu học từ ngày '.$term->start_date->format('d/m/Y').'; ngày bắt đầu của từng lớp xem khi đăng ký học phần.';
        $period = $this->registrationPeriodFor($framework);
        $tz = config('studentmanager.display_timezone');
        $lines[] = $period !== null
            ? 'Đăng ký học phần ('.$period->name.'): từ '.$period->opens_at->setTimezone($tz)->format('H:i d/m/Y').' đến '.$period->closes_at->setTimezone($tz)->format('H:i d/m/Y').' tại mục "Đăng ký học phần" trên cổng sinh viên.'
            : 'Thời gian đăng ký học phần sẽ được thông báo sau.';

        return [
            'title' => "Mở lớp học phần {$termLabel} – khóa {$framework->cohort->code} {$framework->faculty->name}",
            'body' => implode("\n", $lines),
            'students' => $this->studentCount($framework),
        ];
    }

    /** Gửi thông báo cho sinh viên đang học của khoa – khóa (người gửi cần NOT.create trong phạm vi). */
    public function notify(User $actor, TrainingFramework $framework, string $title, string $body): Notification
    {
        $notification = $this->announcements->create($actor, [
            'title' => $title,
            'body' => $body,
            'audience' => ['targets' => [['type' => 'faculty', 'id' => $framework->faculty_id, 'cohort_id' => $framework->cohort_id]]],
            'link' => route('student.pages.registration', absolute: false),
        ]);

        $framework->update(['notified_at' => now()]);

        return $notification;
    }

    public function openedCount(TrainingFrameworkItem $item): int
    {
        return $item->sections()->where('status', '!=', SectionStatus::Cancelled->value)->count();
    }

    /** Số sinh viên đang học / bảo lưu của khoa – khóa. */
    public function studentCount(TrainingFramework $framework): int
    {
        $framework->loadMissing('cohort');

        return $this->studentsOf($framework->faculty_id, $framework->cohort);
    }

    public function studentsOf(int $facultyId, Cohort $cohort): int
    {
        return Student::query()
            ->whereIn('status', [StudentStatus::Studying->value, StudentStatus::Suspended->value])
            ->where('cohort', $cohort->start_year)
            ->whereIn('major_id', Major::query()->select('id')->where('faculty_id', $facultyId))
            ->count();
    }

    /**
     * Chọn học kỳ + khóa, mọi khoa: mỗi khoa có ngành (CTĐT đang áp dụng cho khóa) kèm học kỳ thứ mấy của khóa, số sinh
     * viên, khung đã lập (nếu có) và danh sách môn — của khung đã lập, hoặc xem trước từ CTĐT khi chưa lập.
     *
     * @return list<array{faculty: Faculty, planned_term: ?int, students: int, framework: ?TrainingFramework, subjects: list<array{code: string, name: string, credits: int, compulsory: bool}>}>
     */
    public function facultyOverview(Term $term, Cohort $cohort): array
    {
        $plannedTerm = $this->cohortTermNumber($cohort, $term);
        $frameworks = TrainingFramework::query()->with('items.subject')
            ->where(['term_id' => $term->id, 'cohort_id' => $cohort->id])->get()->keyBy('faculty_id');
        $rows = [];

        foreach (Faculty::query()->whereHas('majors')->orderBy('code')->get() as $faculty) {
            $framework = $frameworks->get($faculty->id);
            $subjects = $framework !== null
                ? $framework->items->map(fn (TrainingFrameworkItem $i) => ['code' => $i->subject->code, 'name' => $i->subject->name, 'credits' => (int) $i->subject->credits, 'compulsory' => (bool) $i->compulsory])
                : $this->curriculumSubjects($faculty->id, $cohort, $plannedTerm);

            if ($framework === null && $subjects->isEmpty()) {
                continue; // Khoa không có ngành / CTĐT cho khóa này
            }

            $rows[] = [
                'faculty' => $faculty, 'planned_term' => $plannedTerm, 'students' => $this->studentsOf($faculty->id, $cohort),
                'framework' => $framework,
                'subjects' => $subjects->sortBy([['compulsory', 'desc'], ['code', 'asc']])->values()->all(),
            ];
        }

        return $rows;
    }

    /**
     * Lập khung cho mọi khoa còn thiếu ở học kỳ + khóa (mỗi khung lấy môn theo CTĐT). Khoa đã có khung giữ nguyên.
     *
     * @return list<TrainingFramework>
     */
    public function createForAllFaculties(Term $term, Cohort $cohort): array
    {
        $this->assertTermOpen($term);

        return $this->transaction(function () use ($term, $cohort): array {
            $created = [];

            foreach ($this->facultyOverview($term, $cohort) as $row) {
                if ($row['framework'] === null) {
                    $framework = $this->create(['faculty_id' => $row['faculty']->id, 'cohort_id' => $cohort->id, 'term_id' => $term->id]);
                    $this->suggestFromCurriculum($framework);
                    $created[] = $framework;
                }
            }

            return $created;
        });
    }

    /** @return Collection<int, array{code: string, name: string, credits: int, compulsory: bool}> */
    private function curriculumSubjects(int $facultyId, Cohort $cohort, ?int $plannedTerm): Collection
    {
        if ($plannedTerm === null) {
            return collect();
        }

        $curricula = Curriculum::query()->inForce()->forCohort($cohort->start_year)
            ->whereIn('major_id', Major::query()->where('faculty_id', $facultyId)->select('id'))->pluck('id');

        return CurriculumSubject::query()->with('subject')->whereIn('curriculum_id', $curricula)->where('planned_term', $plannedTerm)->get()
            ->groupBy('subject_id')
            ->map(fn (Collection $rows) => [
                'code' => $rows->first()->subject->code, 'name' => $rows->first()->subject->name, 'credits' => (int) $rows->first()->subject->credits,
                'compulsory' => $rows->contains(fn (CurriculumSubject $c) => $c->curriculum_elective_group_id === null),
            ])
            ->values();
    }

    /**
     * Các khóa đang có sinh viên của khoa ở học kỳ `$term`, kèm học kỳ thứ mấy của khóa và khung đã lập (nếu có) — để chọn
     * học kỳ + khoa là thấy ngay khung của từng khóa.
     *
     * @return list<array{cohort: Cohort, planned_term: ?int, students: int, framework: ?TrainingFramework}>
     */
    public function cohortOptions(Term $term, int $facultyId): array
    {
        $frameworks = TrainingFramework::query()->withCount('items')
            ->where(['term_id' => $term->id, 'faculty_id' => $facultyId])->get()->keyBy('cohort_id');
        $rows = [];

        foreach (Cohort::query()->orderByDesc('start_year')->get() as $cohort) {
            $students = $this->studentsOf($facultyId, $cohort);

            if ($students > 0 || $frameworks->has($cohort->id)) {
                $rows[] = ['cohort' => $cohort, 'planned_term' => $this->cohortTermNumber($cohort, $term), 'students' => $students, 'framework' => $frameworks->get($cohort->id)];
            }
        }

        return $rows;
    }

    /** Kỳ CTĐT của khung: kỳ Phòng Đào tạo đã chọn, trống thì học kỳ thứ n của khóa. */
    public function plannedTermOf(TrainingFramework $framework): ?int
    {
        $framework->loadMissing(['cohort', 'term.academicYear']);

        return $framework->planned_term ?? $this->cohortTermNumber($framework->cohort, $framework->term);
    }

    /**
     * Học kỳ thứ mấy của khóa (1, 2, 3…) tại học kỳ chính `$term`: năm học thứ k (tính từ năm bắt đầu khóa) × 2 + thứ tự
     * học kỳ chính trong năm học. Học kỳ hè hoặc trước khi khóa nhập học: null.
     */
    public function cohortTermNumber(Cohort $cohort, Term $term): ?int
    {
        if ($term->type !== TermType::MAIN) {
            return null;
        }

        $term->loadMissing('academicYear');
        $yearStart = (int) substr((string) $term->academicYear?->name, 0, 4)
            ?: ((int) $term->start_date->format('n') >= 8 ? (int) $term->start_date->format('Y') : (int) $term->start_date->format('Y') - 1);
        $order = Term::query()
            ->where('academic_year_id', $term->academic_year_id)
            ->where('type', TermType::MAIN->value)
            ->where('start_date', '<=', $term->start_date)
            ->count();
        $number = ($yearStart - $cohort->start_year) * 2 + max(1, $order);

        return $number >= 1 ? $number : null;
    }

    private function registrationPeriodFor(TrainingFramework $framework): ?RegistrationPeriod
    {
        return RegistrationPeriod::query()
            ->where('term_id', $framework->term_id)
            ->where('status', '!=', PeriodStatus::Draft->value)
            ->where(fn ($q) => $q->whereDoesntHave('targets')->orWhereHas('targets', fn ($t) => $t->where('cohort_id', $framework->cohort_id)->orWhereNull('cohort_id')))
            ->where('closes_at', '>', CarbonImmutable::now())
            ->orderBy('opens_at')
            ->first();
    }

    private function assertTermOpen(Term $term): void
    {
        if (in_array($term->status, [TermStatus::COMPLETED, TermStatus::LOCKED], true)) {
            $this->fail("Học kỳ {$term->name} đã kết thúc hoặc đã khóa.", 'Chọn học kỳ chưa kết thúc để lập chương trình khung và mở lớp.');
        }
    }

    private function assertMaxSize(mixed $max): void
    {
        $limit = (int) config('studentmanager.course_section.max_size', 200);

        if ($max !== null && ((int) $max < 1 || (int) $max > $limit)) {
            $this->fail("Sĩ số tối đa {$max} không hợp lệ.", "Nhập từ 1 đến {$limit}, hoặc để trống để dùng mặc định (BR-CLS-02).");
        }
    }
}
