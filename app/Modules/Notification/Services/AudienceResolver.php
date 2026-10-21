<?php

namespace App\Modules\Notification\Services;

use App\Models\User;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Enrollment\Enums\EnrollmentStatus;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\Faculty\Models\Major;
use App\Modules\Faculty\Services\FacultyAccess;
use App\Modules\Notification\Models\RecipientGroup;
use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\SchoolClass\Models\Cohort;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\Student\Enums\StudentStatus;
use App\Modules\Student\Models\Student;
use App\Support\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;

/**
 * Tính người nhận của thông báo thủ công (FR-NOT-006, 015) và chặn đối tượng ngoài phạm vi người gửi (BR-NOT-05).
 *
 * Bộ chọn: `{"targets": [{"type": "all|faculty|major|cohort|admin_class|course_section", "id": 1}], "student_codes": [...],
 * "group_id": 3}`. Người nhận là tài khoản của sinh viên đang học / bảo lưu thuộc các đối tượng đã chọn.
 * - Toàn trường, khóa: cần NOT.create toàn trường (Phòng Đào tạo, CTSV…).
 * - Khoa, ngành: toàn trường, hoặc lãnh đạo của khoa đó.
 * - Lớp hành chính, lớp học phần, danh sách MSSV: đối tượng phải nằm trong phạm vi NOT.create của người gửi (khoa: lớp của
 *   khoa; giảng viên: lớp mình dạy; cố vấn: lớp mình cố vấn). Một mục ngoài phạm vi thì từ chối cả lần gửi.
 * Cuối cùng vẫn lọc lại sinh viên theo phạm vi NOT.create để không lọt người ngoài phạm vi.
 */
class AudienceResolver extends BaseService
{
    public const TYPES = [
        'all' => 'Toàn trường',
        'faculty' => 'Khoa',
        'major' => 'Ngành',
        'cohort' => 'Khóa',
        'admin_class' => 'Lớp hành chính',
        'course_section' => 'Lớp học phần',
    ];

    public function __construct(
        private readonly AccessControl $access,
        private readonly FacultyAccess $faculties,
    ) {}

    /** @return array{user_ids: list<int>, students: int, labels: list<string>, audience: array<string, mixed>} */
    public function resolve(User $actor, array $audience): array
    {
        $audience = $this->normalize($actor, $audience);

        if ($audience['targets'] === [] && $audience['student_codes'] === []) {
            $this->fail('Chưa chọn đối tượng nhận thông báo.', 'Chọn ít nhất một khoa, ngành, khóa, lớp hoặc nhập danh sách MSSV.');
        }

        $global = $this->access->allowsGlobally($actor, 'NOT', PermissionAction::Create);
        $labels = [];
        $query = Student::query()->whereIn('status', [StudentStatus::Studying->value, StudentStatus::Suspended->value])->whereNotNull('user_id');

        $query->where(function (Builder $any) use ($audience, $actor, $global, &$labels): void {
            foreach ($audience['targets'] as $target) {
                [$label, $apply] = $this->target($actor, $global, $target['type'], $target['id'] ?? null);
                $labels[] = $label;
                $any->orWhere(fn (Builder $q) => $apply($q));
            }

            if ($audience['student_codes'] !== []) {
                $codes = $audience['student_codes'];
                $inScope = $this->access->constrain(Student::query(), $actor, 'NOT', PermissionAction::Create)
                    ->whereIn('student_code', $codes)->pluck('student_code')->all();
                $outside = array_values(array_diff($codes, $inScope));

                if ($outside !== []) {
                    $this->fail('Có MSSV không tồn tại hoặc ngoài phạm vi của bạn: '.implode(', ', array_slice($outside, 0, 10)).(count($outside) > 10 ? '…' : '').'.',
                        'Chỉ gửi cho sinh viên thuộc phạm vi quản lý / lớp bạn dạy (BR-NOT-05); bỏ các MSSV này.');
                }

                $labels[] = count($codes).' sinh viên theo danh sách MSSV';
                $any->orWhereIn('student_code', $codes);
            }
        });

        $userIds = $this->access->constrain($query, $actor, 'NOT', PermissionAction::Create)->pluck('user_id')
            ->map(fn ($id) => (int) $id)->unique()->values()->all();

        return ['user_ids' => $userIds, 'students' => count($userIds), 'labels' => $labels, 'audience' => $audience];
    }

    /**
     * Danh sách đối tượng người gửi được chọn (cho giao diện): lớp học phần / lớp hành chính trong phạm vi, khoa / ngành /
     * khóa khi có quyền.
     *
     * @return array<string, list<array{id: int, label: string}>>
     */
    public function options(User $actor): array
    {
        $global = $this->access->allowsGlobally($actor, 'NOT', PermissionAction::Create);
        $facultyIds = $global ? null : $this->faculties->facultyIds($actor);

        return [
            'all' => $global ? [['id' => 0, 'label' => 'Toàn bộ sinh viên đang học']] : [],
            'faculty' => Faculty::query()->when($facultyIds !== null, fn ($q) => $q->whereIn('id', $facultyIds))->orderBy('name')->get(['id', 'code', 'name'])
                ->map(fn ($f) => ['id' => $f->id, 'label' => "{$f->code} – {$f->name}"])->all(),
            'major' => Major::query()->when($facultyIds !== null, fn ($q) => $q->whereIn('faculty_id', $facultyIds))->orderBy('code')->get(['id', 'code', 'name'])
                ->map(fn ($m) => ['id' => $m->id, 'label' => "{$m->code} – {$m->name}"])->all(),
            'cohort' => $global ? Cohort::query()->orderByDesc('start_year')->get(['id', 'code'])->map(fn ($c) => ['id' => $c->id, 'label' => $c->code])->all() : [],
            'admin_class' => $this->access->constrain(AdminClass::query(), $actor, 'NOT', PermissionAction::Create)->where('status', 'active')->orderBy('code')->get(['id', 'code', 'name'])
                ->map(fn ($c) => ['id' => $c->id, 'label' => "{$c->code} – {$c->name}"])->all(),
            'course_section' => $this->access->constrain(CourseSection::query(), $actor, 'NOT', PermissionAction::Create)
                ->whereIn('status', ['registration_open', 'registration_closed', 'in_progress', 'finished'])
                ->with('subject:id,name')->orderByDesc('term_id')->orderBy('code')->limit(500)->get(['id', 'code', 'subject_id'])
                ->map(fn ($s) => ['id' => $s->id, 'label' => "{$s->code} – {$s->subject?->name}"])->all(),
        ];
    }

    /** @return array{targets: list<array{type: string, id: ?int}>, student_codes: list<string>} */
    private function normalize(User $actor, array $audience): array
    {
        $targets = collect($audience['targets'] ?? [])->map(fn ($t) => ['type' => (string) ($t['type'] ?? ''), 'id' => isset($t['id']) ? (int) $t['id'] : null]);
        $codes = collect($audience['student_codes'] ?? [])->map(fn ($c) => trim((string) $c))->filter();

        if (! empty($audience['group_id'])) {
            $group = RecipientGroup::query()->where('owner_id', $actor->id)->find($audience['group_id']);

            if ($group === null) {
                $this->fail('Không tìm thấy nhóm người nhận.', 'Chọn nhóm do chính bạn đã lưu.');
            }

            $targets = $targets->merge($group->audience['targets'] ?? []);
            $codes = $codes->merge($group->audience['student_codes'] ?? []);
        }

        foreach ($targets as $t) {
            if (! isset(self::TYPES[$t['type']])) {
                $this->fail("Loại đối tượng \"{$t['type']}\" không hợp lệ.", 'Chọn một trong: '.implode(', ', array_keys(self::TYPES)).'.');
            }
        }

        return [
            'targets' => $targets->unique(fn ($t) => $t['type'].':'.$t['id'])->values()->all(),
            'student_codes' => $codes->unique()->values()->all(),
        ];
    }

    /** @return array{0: string, 1: callable(Builder): void} nhãn và điều kiện lọc sinh viên của một đối tượng */
    private function target(User $actor, bool $global, string $type, ?int $id): array
    {
        $deny = fn (string $what) => $this->fail("{$what} ngoài phạm vi bạn được gửi thông báo.",
            'Chỉ chọn đối tượng thuộc phạm vi của bạn (BR-NOT-05): giảng viên — lớp mình dạy; khoa — sinh viên của khoa.');

        switch ($type) {
            case 'all':
                $global || $deny('Toàn trường');

                return ['Toàn bộ sinh viên đang học', fn (Builder $q) => $q->whereRaw('1 = 1')];

            case 'cohort':
                $cohort = Cohort::query()->findOrFail($id);
                $global || $deny("Khóa {$cohort->code}");

                return ["Khóa {$cohort->code}", fn (Builder $q) => $q->where('cohort', $cohort->start_year)];

            case 'faculty':
                $faculty = Faculty::query()->findOrFail($id);
                ($global || in_array($faculty->id, $this->faculties->facultyIds($actor), true)) || $deny($faculty->name);

                return [$faculty->name, fn (Builder $q) => $q->whereIn('major_id', Major::query()->select('id')->where('faculty_id', $faculty->id))];

            case 'major':
                $major = Major::query()->findOrFail($id);
                ($global || in_array($major->faculty_id, $this->faculties->facultyIds($actor), true)) || $deny("Ngành {$major->name}");

                return ["Ngành {$major->name}", fn (Builder $q) => $q->where('major_id', $major->id)];

            case 'admin_class':
                $class = AdminClass::query()->findOrFail($id);
                $this->access->allowsOn($actor, 'NOT', PermissionAction::Create, $class) || $deny("Lớp {$class->code}");

                return ["Lớp {$class->code}", fn (Builder $q) => $q->whereHas('adminClassMembers', fn (Builder $m) => $m->current()->where('admin_class_id', $class->id))];

            default: // course_section
                $section = CourseSection::query()->findOrFail($id);
                $this->access->allowsOn($actor, 'NOT', PermissionAction::Create, $section) || $deny("Lớp học phần {$section->code}");

                return ["Lớp học phần {$section->code}", fn (Builder $q) => $q->whereIn('id', fn ($e) => $e->select('student_id')->from('enrollments')
                    ->where('course_section_id', $section->id)->whereIn('status', EnrollmentStatus::activeValues())->whereNull('deleted_at'))];
        }
    }
}
