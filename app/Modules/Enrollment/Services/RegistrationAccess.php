<?php

namespace App\Modules\Enrollment\Services;

use App\Models\User;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\Enrollment\Models\RegistrationPeriodTarget;
use App\Modules\SchoolClass\Models\AdminClassMember;
use App\Modules\Student\Enums\StudentStatus;
use App\Modules\Student\Models\Student;
use App\Support\Services\BaseService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Sinh viên có được đăng ký trong đợt nào không (BR-ENR-01, BR-ENR-11): trạng thái Đang học, thuộc đối tượng của đợt
 * (khóa / ngành / lớp hành chính hiện tại), đợt đã công bố và đang mở theo giờ máy chủ.
 */
class RegistrationAccess extends BaseService
{
    public function studentOf(User $user): Student
    {
        $student = Student::query()->where('user_id', $user->id)->first();

        if ($student === null) {
            $this->fail('Tài khoản này không gắn với hồ sơ sinh viên.', 'Đăng nhập bằng tài khoản sinh viên (MSSV) để đăng ký học phần.');
        }

        return $student;
    }

    /** Thời điểm hiện tại theo máy chủ (BR-ENR-11): không nhận thời gian từ trình duyệt. */
    public function now(): CarbonImmutable
    {
        return CarbonImmutable::now('UTC');
    }

    /** @return Collection<int, RegistrationPeriod> các đợt đang mở mà sinh viên thuộc đối tượng */
    public function openPeriodsFor(Student $student): Collection
    {
        return RegistrationPeriod::query()
            ->with('targets', 'term')
            ->openAt($this->now())
            ->orderBy('opens_at')
            ->get()
            ->filter(fn (RegistrationPeriod $p) => $this->targets($p, $student))
            ->values();
    }

    /** Sinh viên thuộc đối tượng của đợt. */
    public function targets(RegistrationPeriod $period, Student $student): bool
    {
        if ($period->training_framework_id !== null) {
            $period->loadMissing('framework.cohort');
            if ($period->framework === null || $period->framework->cohort->start_year !== $student->cohort
                || $student->major?->faculty_id !== $period->framework->faculty_id) {
                return false;
            }
        }
        $period->loadMissing('targets.cohort');

        if ($period->targets->isEmpty()) {
            return true;
        }

        $classId = $this->currentClassId($student);

        return $period->targets->contains(fn (RegistrationPeriodTarget $t) => ($t->cohort_id === null || $t->cohort?->start_year === $student->cohort)
            && ($t->major_id === null || $t->major_id === $student->major_id)
            && ($t->admin_class_id === null || $t->admin_class_id === $classId));
    }

    /** Đợt đã thông báo để xem trước, hoặc đã công bố; tuyệt đối không lộ bản nháp. */
    public function visiblePeriodsFor(Student $student): Collection
    {
        return RegistrationPeriod::query()->with('targets', 'term', 'framework.cohort')
            ->where(fn ($q) => $q->where('status', 'published')->orWhere(fn ($q) => $q->where('status', 'draft')->whereNotNull('preview_at')))
            ->where('closes_at', '>', $this->now())->orderBy('opens_at')->get()
            ->filter(fn (RegistrationPeriod $p) => $this->targets($p, $student))->values();
    }

    /**
     * Lý do sinh viên không được đăng ký trong đợt; rỗng là được (BR-ENR-01). #105 gọi trước mỗi lần đăng ký.
     *
     * @return list<string>
     */
    public function problems(RegistrationPeriod $period, Student $student): array
    {
        $problems = [];

        if ($student->status !== StudentStatus::Studying) {
            $problems[] = "Sinh viên đang ở trạng thái {$student->status->label()}, chỉ sinh viên Đang học được đăng ký (BR-ENR-01).";
        }

        if (! $this->targets($period, $student)) {
            $problems[] = "Sinh viên không thuộc đối tượng của đợt {$period->name}.";
        }

        if ($period->status->value === 'draft') {
            $problems[] = 'Danh sách chỉ để xem trước. Phòng Đào tạo chưa công bố mở đăng ký.';
        } elseif (! $period->isOpenAt($this->now())) {
            $tz = config('studentmanager.display_timezone');
            $problems[] = "Đợt {$period->name} mở từ ".$period->opens_at->setTimezone($tz)->format('H:i d/m/Y')
                .' đến '.$period->closes_at->setTimezone($tz)->format('H:i d/m/Y').' (giờ máy chủ, BR-ENR-11).';
        }

        return $problems;
    }

    public function assertCanRegister(RegistrationPeriod $period, Student $student): void
    {
        $problems = $this->problems($period, $student);

        if ($problems !== []) {
            $this->fail(implode(' ', $problems), 'Liên hệ Phòng Đào tạo nếu cần đăng ký ngoài đợt (đăng ký thay, FR-ENR-009).');
        }
    }

    private function currentClassId(Student $student): ?int
    {
        return AdminClassMember::query()->where('student_id', $student->id)->current()->value('admin_class_id');
    }
}
