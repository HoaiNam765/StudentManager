<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Modules\AcademicYear\Models\Term;
use App\Modules\Auth\Enums\PermissionAction;
use App\Modules\Auth\Services\AccessControl;
use App\Modules\Auth\Services\LoginService;
use App\Modules\Auth\Services\PasswordService;
use App\Modules\Curriculum\Services\CurriculumService;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\Enrollment\Models\RegistrationPeriod;
use App\Modules\Enrollment\Services\CreditLimits;
use App\Modules\Enrollment\Services\EnrollmentRules;
use App\Modules\Faculty\Models\Major;
use App\Modules\Grade\Events\GradesPublished;
use App\Modules\Grade\Models\GradeVersion;
use App\Modules\Grade\Models\SectionGradeComponent;
use App\Modules\Grade\Models\SectionGradeWorkflow;
use App\Modules\Grade\Models\TermGpa;
use App\Modules\Grade\Services\CourseGradeService;
use App\Modules\Grade\Services\GradebookService;
use App\Modules\Grade\Services\GradeChangeService;
use App\Modules\Grade\Services\TranscriptService;
use App\Modules\Notification\Models\Notification;
use App\Modules\SchoolClass\Enums\SectionStatus;
use App\Modules\SchoolClass\Models\CourseSection;
use App\Modules\SchoolClass\Models\SectionInstructor;
use App\Modules\SchoolClass\Services\CourseSectionService;
use App\Modules\Student\Enums\StudentStatus;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Services\AdmissionService;
use App\Modules\Student\Services\StudentStatusService;
use App\Modules\Subject\Enums\ComponentKind;
use App\Modules\Subject\Enums\RelationType;
use App\Modules\Subject\Models\Subject;
use App\Modules\Subject\Services\GradeStructureService;
use App\Modules\Subject\Services\SubjectRelationService;
use App\Modules\System\Services\ImportService;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Timetable\Models\ScheduleRule;
use App\Modules\Timetable\Services\ScheduleConflictService;
use App\Modules\Timetable\Services\ScheduleRuleService;
use App\Support\Exceptions\BusinessRuleException;
use Database\Seeders\DevRoleUsersSeeder;
use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Nghiệm thu P1 (#123; BA 11.2 UAT-01 → UAT-08): chạy các kịch bản qua dịch vụ thật trên CSDL hiện tại (nên là CSDL demo
 * sau `migrate --seed` + `demo:seed`), **trong một giao dịch rồi hoàn tác** nên không để lại dữ liệu. In từng kiểm tra
 * ĐẠT / LỖI / BỎ QUA kèm chi tiết và ghi báo cáo Markdown vào storage/logs/uat-p1-*.md.
 *
 * Không chạy ở production. Kiểm thử đồng thời của UAT-06 (100 yêu cầu vào một chỗ) cần nhiều tiến trình nên không chạy
 * trong lệnh này (đã đo ở #105: 12 tiến trình song song chỉ 1 thành công).
 */
class RunP1Acceptance extends Command
{
    protected $signature = 'uat:p1';

    protected $description = 'Chạy kịch bản nghiệm thu P1 (UAT-01 → UAT-08) rồi hoàn tác, in kết quả';

    /** @var list<array{uat: string, name: string, status: string, detail: string}> */
    private array $results = [];

    public function handle(): int
    {
        if ($this->laravel->isProduction()) {
            $this->error('Không chạy nghiệm thu trên môi trường production.');

            return self::FAILURE;
        }

        config(['mail.default' => 'array']);
        DB::beginTransaction();

        try {
            $this->uat01();
            $this->uat02();
            $this->uat03();
            $this->uat04();
            $this->uat05();
            $this->uat06();
            [$section, $student, $lecturer] = $this->uat07();
            $this->uat08($section, $student, $lecturer);
        } finally {
            DB::rollBack();
            Auth::guard('web')->logout();
        }

        $this->table(['UAT', 'Kiểm tra', 'Kết quả', 'Chi tiết'], array_map(fn ($r) => [$r['uat'], $r['name'], $r['status'], mb_strimwidth($r['detail'], 0, 110, '…')], $this->results));
        $failed = count(array_filter($this->results, fn ($r) => $r['status'] === 'LỖI'));
        $path = storage_path('logs/uat-p1-'.now()->format('Ymd-His').'.md');
        file_put_contents($path, $this->report());
        $this->line("Báo cáo: {$path}");
        $failed === 0 ? $this->info('Tất cả kiểm tra đạt.') : $this->error("{$failed} kiểm tra không đạt.");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }

    // ---------------------------------------------------------------- UAT-01

    private function uat01(): void
    {
        $user = $this->studentUser();

        $this->check('UAT-01', 'Khóa tạm sau 5 lần nhập sai mật khẩu', function () use ($user): string {
            $max = (int) config('studentmanager.auth.lockout_max_attempts');

            for ($i = 0; $i < $max; $i++) {
                try {
                    app(LoginService::class)->attempt($user->username, 'sai-mat-khau-'.$i);
                } catch (ValidationException) {
                }
            }

            $user->refresh();
            $this->assert($max === 5, "Cấu hình khóa sau {$max} lần (BA: 5).");
            $this->assert($user->locked_until !== null && $user->locked_until->isFuture(), 'Tài khoản chưa bị khóa tạm.');

            try {
                app(LoginService::class)->attempt($user->username, DevRoleUsersSeeder::PASSWORD);
                $this->assert(false, 'Đang khóa mà vẫn đăng nhập được bằng mật khẩu đúng.');
            } catch (ValidationException) {
            }

            $until = $user->locked_until->timezone(config('studentmanager.display_timezone'))->format('H:i');
            app(LoginService::class)->unlock($user);

            return "Sau {$max} lần sai, khóa tới {$until}; mật khẩu đúng cũng bị từ chối khi đang khóa.";
        });

        $this->check('UAT-01', 'Liên kết đặt lại mật khẩu dùng một lần, hết hạn sau 30 phút', function () use ($user): string {
            $this->assert((int) config('auth.passwords.users.expire') === 30, 'Hạn liên kết không phải 30 phút.');
            $broker = Password::broker();
            $token = $broker->createToken($user);
            $credentials = ['email' => $user->email, 'password' => 'Uat#2026Reset!', 'password_confirmation' => 'Uat#2026Reset!', 'token' => $token];
            $reset = fn () => $broker->reset($credentials, fn (User $u, string $p) => app(PasswordService::class)->reset($u, $p));

            $this->assert($reset() === Password::PASSWORD_RESET, 'Lần dùng đầu không đặt lại được.');
            $this->assert($reset() === Password::INVALID_TOKEN, 'Dùng lại liên kết lần hai vẫn được.');

            $token = $broker->createToken($user->refresh());
            DB::table('password_reset_tokens')->where('email', $user->email)->update(['created_at' => now()->subMinutes(31)]);
            $this->assert(! $broker->tokenExists($user, $token), 'Liên kết quá 30 phút vẫn còn hiệu lực.');

            return 'Lần 1 đặt lại được, lần 2 báo liên kết không hợp lệ; sau 31 phút liên kết hết hạn.';
        });
    }

    // ---------------------------------------------------------------- UAT-02

    private function uat02(): void
    {
        $access = app(AccessControl::class);

        $this->check('UAT-02', 'Giảng viên chỉ thấy lớp mình dạy', function () use ($access): string {
            $lecturer = Lecturer::query()->whereNotNull('user_id')->whereIn('id', SectionInstructor::query()->select('lecturer_id'))->orderBy('code')->firstOrFail();
            $user = User::query()->findOrFail($lecturer->user_id);
            $access->flush();
            $visible = $access->constrain(CourseSection::query(), $user, 'CLS', PermissionAction::View)->pluck('id')->sort()->values()->all();
            $assigned = SectionInstructor::query()->where('lecturer_id', $lecturer->id)->pluck('course_section_id')->unique()->sort()->values()->all();
            $this->assert($visible === $assigned, 'Danh sách lớp thấy được khác lớp được phân công.');

            return "{$lecturer->code} thấy ".count($visible).' / '.CourseSection::query()->count().' lớp học phần (đúng các lớp được phân công).';
        });

        $this->check('UAT-02', 'Sinh viên không xem được hồ sơ sinh viên khác', function () use ($access): string {
            [$a, $b] = Student::query()->whereNotNull('user_id')->orderBy('id')->take(2)->get()->all();
            $user = User::query()->findOrFail($a->user_id);
            $access->flush();
            $this->assert($access->allowsOn($user, 'STU', PermissionAction::View, $a), 'Không xem được hồ sơ của chính mình.');
            $this->assert(! $access->allowsOn($user, 'STU', PermissionAction::View, $b), 'Xem được hồ sơ người khác.');

            return "{$a->student_code} xem được hồ sơ mình, không xem được {$b->student_code}.";
        });

        $this->check('UAT-02', 'Truy cập trái quyền bị từ chối và ghi nhật ký', function (): string {
            $user = $this->studentUser();
            $before = DB::table('audit_logs')->where('event', 'access_denied')->count();
            $status = $this->httpAs($user, 'GET', '/admin/grade-change-requests');
            $after = DB::table('audit_logs')->where('event', 'access_denied')->count();
            $this->assert($status === 403, "Mã trả về {$status}, mong đợi 403.");
            $this->assert($after === $before + 1, 'Không có dòng nhật ký "Từ chối truy cập".');

            return 'Sinh viên gọi API duyệt điều chỉnh điểm: 403, có nhật ký "Từ chối truy cập".';
        });
    }

    // ---------------------------------------------------------------- UAT-03

    private function uat03(): void
    {
        $this->check('UAT-03', 'Chặn vòng lặp tiên quyết', function (): string {
            [$a, $b, $c] = Subject::query()->where('status', 'active')->whereNotExists(fn ($q) => $q->from('subject_relations')->whereNull('subject_relations.deleted_at')->where(fn ($w) => $w->whereColumn('subject_relations.subject_id', 'subjects.id')->orWhereColumn('subject_relations.related_subject_id', 'subjects.id')))->orderBy('code')->take(3)->get()->all();
            $relations = app(SubjectRelationService::class);
            $relations->add($a, $b, RelationType::Prerequisite);
            $relations->add($b, $c, RelationType::Prerequisite);
            $message = $this->expectRejected(fn () => $relations->add($c, $a, RelationType::Prerequisite));

            return "{$a->code} ← {$b->code} ← {$c->code}, thêm {$c->code} ← {$a->code}: {$message}";
        });

        $this->check('UAT-03', 'Tổng trọng số điểm phải bằng 100%', function (): string {
            $message = $this->expectRejected(fn () => app(GradeStructureService::class)->validate([
                ['name' => 'Chuyên cần', 'kind' => 'attendance', 'weight' => 10, 'is_required' => true],
                ['name' => 'Giữa kỳ', 'kind' => 'midterm', 'weight' => 20, 'is_required' => true],
                ['name' => 'Cuối kỳ', 'kind' => 'final_exam', 'weight' => 60, 'is_required' => true],
            ]));

            return "10 + 20 + 60 = 90%: {$message}";
        });

        $this->check('UAT-03', 'CTĐT thiếu tín chỉ không ban hành được', function (): string {
            $major = Major::query()->orderBy('code')->firstOrFail();
            $curricula = app(CurriculumService::class);
            $curriculum = $curricula->create([
                'code' => 'CTDT-UAT-'.$major->code, 'name' => 'CTĐT thử nghiệm UAT', 'major_id' => $major->id,
                'training_type_id' => DB::table('training_types')->where('code', 'CQ')->value('id'), 'cohort_from' => 2030,
            ]);
            $message = $this->expectRejected(fn () => $curricula->publish($curriculum));

            return "CTĐT rỗng yêu cầu {$curriculum->total_credits} tín chỉ: {$message}";
        });
    }

    // ---------------------------------------------------------------- UAT-04

    private function uat04(): void
    {
        $this->check('UAT-04', 'Cấp MSSV đúng quy tắc khi xác nhận nhập học', function (): string {
            $student = Student::factory()->cohort(2026)->create(['major_id' => Major::query()->orderBy('code')->value('id')]);
            $student = app(AdmissionService::class)->admit($student->refresh(), null, '2026-09-05');
            $major = substr(preg_replace('/\D/', '', $student->major->code), -4);
            $this->assert((bool) preg_match('/^26'.$major.'\d{4}$/', $student->student_code), "MSSV {$student->student_code} sai quy tắc.");
            $this->assert($student->user_id !== null && $student->status === StudentStatus::Studying, 'Chưa có tài khoản hoặc chưa Đang học.');

            return "MSSV {$student->student_code} = khóa 26 + 4 số cuối mã ngành {$major} + số thứ tự; có tài khoản, Đang học.";
        });

        $this->check('UAT-04', 'Import hồ sơ báo lỗi theo dòng – cột', function (): string {
            $csv = "full_name,date_of_birth,major_code,training_type_code,cohort\n"
                .'Nguyễn Văn Hợp Lệ,2008-03-04,'.Major::query()->orderBy('code')->value('code').",CQ,2026\n"
                ."Trần Thị Sai Ngày,31/02/2008,XXXX,CQ,2026\n";
            $path = tempnam(sys_get_temp_dir(), 'uat').'.csv';
            file_put_contents($path, $csv);
            $imports = app(ImportService::class);
            $admin = User::query()->where('username', 'admin')->firstOrFail();
            $batch = $imports->validate($imports->upload('students', new UploadedFile($path, 'sinh-vien.csv', 'text/csv', null, true), $admin), $admin);
            $errors = $imports->preview($batch)['error_rows'];
            $this->assert($errors->count() === 1 && (int) $errors->first()->row_number === 3, 'Không báo đúng dòng lỗi (dòng 3).');
            $columns = collect($errors->first()->errors)->pluck('column')->unique()->implode(', ');

            return "Dòng 2 hợp lệ; dòng 3 lỗi ở cột: {$columns}.";
        });

        $this->check('UAT-04', 'Bảo lưu hủy các đăng ký chưa chốt', function (): string {
            $student = Student::query()->where('status', StudentStatus::Studying)->where('cohort', 2026)->orderByDesc('id')->firstOrFail();
            $section = CourseSection::query()->where('status', SectionStatus::RegistrationOpen)->firstOrFail();
            $id = DB::table('enrollments')->insertGetId([
                'student_id' => $student->id, 'course_section_id' => $section->id, 'term_id' => $section->term_id, 'subject_id' => $section->subject_id,
                'type' => 'normal', 'status' => 'registered', 'source' => 'student', 'registered_at' => now(), 'created_at' => now(), 'updated_at' => now(),
            ]);
            app(StudentStatusService::class)->change($student, StudentStatus::Suspended, [
                'decision_no' => 'QĐ-UAT-04', 'effective_date' => now(config('studentmanager.display_timezone'))->toDateString(), 'reason' => 'Bảo lưu (UAT-04)',
            ]);
            $status = DB::table('enrollments')->where('id', $id)->value('status');
            $this->assert($status === 'cancelled', "Đăng ký chưa chốt còn trạng thái {$status}.");

            return "{$student->student_code} bảo lưu: đăng ký lớp {$section->code} (chưa chốt) chuyển sang Đã hủy.";
        });
    }

    // ---------------------------------------------------------------- UAT-05

    private function uat05(): void
    {
        $this->check('UAT-05', 'Chặn mở đăng ký lớp chưa có giảng viên chính', function (): string {
            $section = $this->newSection(SectionStatus::Planned, withLecturer: false);
            $message = $this->expectRejected(fn () => app(CourseSectionService::class)->advance($section, SectionStatus::RegistrationOpen));

            return "Lớp {$section->code}: {$message}";
        });

        $this->check('UAT-05', 'Phát hiện trùng phòng, trùng giảng viên', function (): string {
            $rule = ScheduleRule::query()->whereNotNull('room_id')->with('section')->firstOrFail();
            $lecturerId = SectionInstructor::query()->where('course_section_id', $rule->course_section_id)->value('lecturer_id');
            $other = $this->newSection(SectionStatus::Planned, withLecturer: false, term: $rule->section->term);
            SectionInstructor::query()->create(['course_section_id' => $other->id, 'lecturer_id' => $lecturerId, 'role' => 'main']);
            $candidate = new ScheduleRule($rule->only('day_of_week', 'period_from', 'period_to', 'room_id', 'week_from', 'week_to', 'week_parity') + ['course_section_id' => $other->id]);
            $found = app(ScheduleConflictService::class)->check($other, $candidate);
            $messages = collect([...$found['block'], ...$found['warn']]);
            $room = $messages->first(fn (string $m) => str_starts_with($m, 'Phòng'));
            $teacher = $messages->first(fn (string $m) => str_starts_with($m, 'Giảng viên'));
            $this->assert($room !== null, 'Không phát hiện trùng phòng.');
            $this->assert($teacher !== null, 'Không phát hiện trùng giảng viên.');

            return "{$room} {$teacher}";
        });

        $this->check('UAT-05', 'Phòng không đủ sức chứa', function (): string {
            $room = DB::table('rooms')->whereNull('deleted_at')->orderBy('capacity')->first();
            $section = $this->newSection(SectionStatus::Planned, withLecturer: true, maxSize: $room->capacity + 20);
            $message = $this->expectRejected(fn () => app(ScheduleRuleService::class)->create($section, [
                'day_of_week' => 7, 'period_from' => 13, 'period_to' => 14, 'room_id' => $room->id, 'week_from' => 1, 'week_to' => 2,
            ]));

            return "Phòng {$room->code} ({$room->capacity} chỗ), lớp tối đa ".($room->capacity + 20).": {$message}";
        });
    }

    // ---------------------------------------------------------------- UAT-06

    private function uat06(): void
    {
        $period = RegistrationPeriod::query()->where('status', 'published')->latest('id')->first();

        if ($period === null) {
            $this->skip('UAT-06', 'Kiểm tra điều kiện đăng ký', 'Không có đợt đăng ký đang mở (chạy migrate --seed).');

            return;
        }

        $student = Student::query()->where('status', StudentStatus::Studying)->where('cohort', 2026)->orderBy('id')->firstOrFail();
        $sections = CourseSection::query()->where('term_id', $period->term_id)->where('status', SectionStatus::RegistrationOpen)->with('subject')->get();
        $rules = app(EnrollmentRules::class);
        $reasons = fn (CourseSection $s) => implode(' | ', $rules->check($student, $s, $period));

        $this->check('UAT-06', 'Chặn thiếu học phần tiên quyết', function () use ($sections, $reasons): string {
            $section = $sections->first(fn (CourseSection $s) => DB::table('subject_relations')->where('subject_id', $s->subject_id)->where('type', 'prerequisite')->whereNull('deleted_at')->exists());
            $this->assert($section !== null, 'Không có lớp nào của học phần có tiên quyết.');
            $text = $reasons($section);
            $this->assert(str_contains(mb_strtolower($text), 'tiên quyết'), "Không báo thiếu tiên quyết: {$text}");

            return "{$section->code}: {$text}";
        });

        $this->check('UAT-06', 'Chặn lớp hết chỗ', function () use ($sections, $student, $reasons): string {
            $section = $sections->first();
            $section->update(['max_size' => 1]);
            $other = Student::query()->where('status', StudentStatus::Studying)->whereKeyNot($student->id)->firstOrFail();
            DB::table('enrollments')->insert(['student_id' => $other->id, 'course_section_id' => $section->id, 'term_id' => $section->term_id, 'subject_id' => $section->subject_id,
                'type' => 'normal', 'status' => 'registered', 'source' => 'student', 'registered_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
            $text = $reasons($section->refresh());
            $this->assert(preg_match('/hết chỗ|đã đầy|đã đủ|đủ sĩ số/u', mb_strtolower($text)) === 1, "Không báo hết chỗ: {$text}");

            return "{$section->code} (tối đa 1, đã có 1): {$text}";
        });

        $this->check('UAT-06', 'Chặn trùng lịch', function () use ($sections, $student, $reasons): string {
            [$a, $b] = $sections->filter(fn ($s) => ScheduleRule::query()->where('course_section_id', $s->id)->exists())->slice(1, 2)->values()->all();
            $rule = ScheduleRule::query()->where('course_section_id', $a->id)->firstOrFail();
            ScheduleRule::query()->where('course_section_id', $b->id)->delete();
            DB::table('schedule_rules')->insert($rule->only('day_of_week', 'period_from', 'period_to', 'week_from', 'week_to', 'week_parity') + ['course_section_id' => $b->id, 'room_id' => null, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('enrollments')->insert(['student_id' => $student->id, 'course_section_id' => $a->id, 'term_id' => $a->term_id, 'subject_id' => $a->subject_id,
                'type' => 'normal', 'status' => 'registered', 'source' => 'student', 'registered_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
            $text = $reasons($b);
            $this->assert(str_contains(mb_strtolower($text), 'trùng lịch'), "Không báo trùng lịch: {$text}");

            return "Đã đăng ký {$a->code}, chọn {$b->code} cùng giờ: {$text}";
        });

        $this->check('UAT-06', 'Chặn vượt số tín chỉ tối đa', function () use ($sections, $student, $reasons, $period): string {
            $max = app(CreditLimits::class)->for($student, $period)['max'];
            $target = $sections->last();

            // Đăng ký thêm các lớp còn lại của học kỳ để tổng tín chỉ vượt khung
            foreach ($sections->slice(3)->reject(fn ($s) => $s->is($target)) as $s) {
                DB::table('enrollments')->insert(['student_id' => $student->id, 'course_section_id' => $s->id, 'term_id' => $s->term_id, 'subject_id' => $s->subject_id,
                    'type' => 'normal', 'status' => 'registered', 'source' => 'student', 'registered_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
            }

            $text = $reasons($target);
            $this->assert(str_contains(mb_strtolower($text), 'tín chỉ'), "Không báo vượt tín chỉ: {$text}");

            return "Tối đa {$max} tín chỉ: {$text}";
        });

        $this->skip('UAT-06', '100 yêu cầu đồng thời vào 1 chỗ cuối', 'Cần nhiều tiến trình song song, không chạy trong một giao dịch; đã đo ở #105 (12 tiến trình, chỉ 1 thành công).');
    }

    // ---------------------------------------------------------------- UAT-07

    /** @return array{0: CourseSection, 1: Student, 2: User} */
    private function uat07(): array
    {
        $student = Student::factory()->cohort(2026)->create(['major_id' => Major::query()->orderBy('code')->value('id')]);
        $student = app(AdmissionService::class)->admit($student->refresh(), null, '2026-09-05');
        $term = Term::query()->orderByDesc('start_date')->firstOrFail();
        $lecturer = null;
        $sections = [];

        $this->check('UAT-07', 'Điểm tổng kết khớp ví dụ BA (9,0 / 7,0 / 6,5 × 10 / 30 / 60 → 6,9 C 2,0)', function () use ($student, $term, &$sections, &$lecturer): string {
            [$section, $enrollment, $lecturer] = $this->gradedSection($term, $student, [9.0, 7.0, 6.5]);
            $sections[] = $section;
            $result = app(CourseGradeService::class)->save($enrollment);
            $this->assert((float) $result['score10'] === 6.9 && $result['letter'] === 'C' && (float) $result['point4'] === 2.0 && $result['is_passed'] === true,
                'Kết quả '.json_encode($result, JSON_UNESCAPED_UNICODE));

            return "Lớp {$section->code}: 6,9 / C / 2,0 / Đạt.";
        });

        $this->check('UAT-07', 'Sinh viên chỉ thấy điểm đã công bố', function () use ($student): string {
            $rows = collect(app(TranscriptService::class)->personal($student)['terms'])->flatMap(fn ($t) => $t['rows']);
            $this->assert($rows->isEmpty(), 'Bảng điểm có học phần chưa công bố.');

            return 'Trước khi công bố, bảng điểm cá nhân trống.';
        });

        $this->check('UAT-07', 'GPA học kỳ 2,50 (B 3 tín chỉ + C 3 tín chỉ), công bố tạo thông báo không kèm điểm', function () use ($student, $term, &$sections): string {
            [$second, $enrollment] = $this->gradedSection($term, $student, [8.0, 8.0, 7.5]);
            app(CourseGradeService::class)->save($enrollment);
            $sections[] = $second;
            $before = Notification::query()->where('event_code', 'EV-GRD-01')->count();

            foreach ($sections as $section) {
                $this->publish($section);
            }

            $gpa = TermGpa::query()->where('student_id', $student->id)->where('term_id', $term->id)->first();
            $this->assert($gpa !== null && (float) $gpa->gpa === 2.5, 'GPA học kỳ '.($gpa?->gpa ?? 'chưa có').', mong đợi 2,50.');
            $notes = Notification::query()->where('event_code', 'EV-GRD-01')->latest('id')->take(2)->get();
            $this->assert(Notification::query()->where('event_code', 'EV-GRD-01')->count() === $before + 2, 'Không tạo thông báo công bố điểm.');
            $this->assert($notes->every(fn ($n) => ! preg_match('/\d+[,.]\d/', $n->body)), 'Nội dung thông báo có điểm.');

            return "GPA học kỳ {$gpa->gpa} ({$gpa->classification}); 2 thông báo công bố, nội dung không có điểm.";
        });

        return [$sections[0] ?? null, $student, $lecturer];
    }

    // ---------------------------------------------------------------- UAT-08

    private function uat08(?CourseSection $section, Student $student, ?User $lecturer): void
    {
        if ($section === null || $lecturer === null) {
            $this->skip('UAT-08', 'Điều chỉnh điểm sau công bố', 'UAT-07 không tạo được lớp đã công bố.');

            return;
        }

        $enrollment = Enrollment::query()->where('course_section_id', $section->id)->where('student_id', $student->id)->firstOrFail();
        $midterm = SectionGradeComponent::query()->where('course_section_id', $section->id)->where('kind', ComponentKind::Midterm->value)->firstOrFail();

        $this->check('UAT-08', 'Không sửa điểm trực tiếp sau công bố', function () use ($section, $lecturer, $enrollment, $midterm): string {
            $message = $this->expectRejected(fn () => app(GradebookService::class)->saveDraft($section, $lecturer, [['enrollment_id' => $enrollment->id, 'component_id' => $midterm->id, 'score' => 9]]));

            return $message;
        });

        $this->check('UAT-08', 'Yêu cầu → duyệt → phiên bản và lịch sử', function () use ($lecturer, $enrollment, $midterm): string {
            config(['grade_lifecycle.faculty_approval_required' => false]);
            $changes = app(GradeChangeService::class);
            $request = $changes->request($enrollment, $lecturer, [['component_id' => $midterm->id, 'score' => 9]], 'Chấm sót câu (UAT-08)', null);
            $acad = User::query()->where('username', 'acad.test')->firstOrFail();
            app(AccessControl::class)->flush();
            $changes->decide($request, $acad, true);
            $version = GradeVersion::query()->where('enrollment_id', $enrollment->id)->latest('version')->first();
            $this->assert($version !== null, 'Không lưu phiên bản điểm.');
            $before = $version->snapshot['before']['result']['score10'] ?? null;
            $after = $version->snapshot['after']['result']['score10'] ?? null;
            $this->assert((float) $after > (float) $before, 'Điểm sau điều chỉnh không đổi.');
            $this->assert(Notification::query()->where('event_code', 'EV-GRD-02')->where('event_key', 'like', "%request:{$request->id}%")->exists(), 'Không có thông báo điều chỉnh điểm.');

            return "Phiên bản {$version->version}: {$before} → {$after}; có thông báo EV-GRD-02.";
        });
    }

    // ---------------------------------------------------------------- Dựng dữ liệu thử

    /** @return array{0: CourseSection, 1: Enrollment, 2: User} lớp có cơ cấu 10/30/60, sinh viên có đủ 3 điểm */
    private function gradedSection(Term $term, Student $student, array $scores): array
    {
        $section = $this->newSection(SectionStatus::InProgress, withLecturer: true, term: $term, credits: 3);
        $components = collect([['Chuyên cần', 'attendance', 10], ['Giữa kỳ', 'midterm', 30], ['Thi cuối kỳ', 'final_exam', 60]])
            ->map(fn ($c, $i) => SectionGradeComponent::query()->create(['course_section_id' => $section->id, 'name' => $c[0], 'kind' => $c[1], 'weight' => $c[2],
                'is_required' => true, 'is_remote' => false, 'sort_order' => $i + 1, 'version' => 1]));
        $enrollment = Enrollment::query()->findOrFail(DB::table('enrollments')->insertGetId([
            'student_id' => $student->id, 'course_section_id' => $section->id, 'term_id' => $section->term_id, 'subject_id' => $section->subject_id,
            'type' => 'normal', 'status' => 'confirmed', 'source' => 'staff', 'registered_at' => now(), 'created_at' => now(), 'updated_at' => now(),
        ]));
        $lecturer = User::query()->findOrFail(Lecturer::query()->whereKey(SectionInstructor::query()->where('course_section_id', $section->id)->value('lecturer_id'))->value('user_id'));
        app(AccessControl::class)->flush();
        app(GradebookService::class)->saveDraft($section, $lecturer, $components->values()->map(fn ($c, $i) => ['enrollment_id' => $enrollment->id, 'component_id' => $c->id, 'score' => $scores[$i]])->all());

        return [$section, $enrollment, $lecturer];
    }

    private function publish(CourseSection $section): void
    {
        $workflow = SectionGradeWorkflow::query()->firstOrCreate(['course_section_id' => $section->id], ['status' => 'draft']);
        $workflow->update(['status' => 'published', 'published_at' => now()]);
        GradesPublished::dispatch($section->id, User::query()->where('username', 'acad.test')->value('id'));
    }

    private function newSection(SectionStatus $status, bool $withLecturer, ?Term $term = null, int $maxSize = 60, ?int $credits = null): CourseSection
    {
        static $n = 0;
        $n++;
        $term ??= Term::query()->orderByDesc('start_date')->firstOrFail();
        $subject = Subject::query()->where('status', 'active')->where('counts_toward_gpa', true)->when($credits, fn ($q) => $q->where('credits', $credits))
            ->orderBy('code')->skip($n)->firstOrFail();
        $section = CourseSection::query()->create([
            'subject_id' => $subject->id, 'term_id' => $term->id, 'sequence' => 90 + $n, 'code' => "{$subject->code}.UAT.{$n}",
            'min_size' => 1, 'max_size' => $maxSize, 'language' => 'vi', 'status' => $status,
        ]);

        if ($withLecturer) {
            $lecturer = Lecturer::query()->whereNotNull('user_id')->working()->orderBy('code')->firstOrFail();
            SectionInstructor::query()->create(['course_section_id' => $section->id, 'lecturer_id' => $lecturer->id, 'role' => 'main']);
        }

        return $section->refresh();
    }

    private function studentUser(): User
    {
        return User::query()->findOrFail(Student::query()->where('status', StudentStatus::Studying)->whereNotNull('user_id')->orderBy('id')->value('user_id'));
    }

    /** Gửi một yêu cầu HTTP nội bộ với người dùng đã đăng nhập; trả mã HTTP. */
    private function httpAs(User $user, string $method, string $uri): int
    {
        Auth::guard('web')->setUser($user);
        app(AccessControl::class)->flush();
        $response = app(HttpKernel::class)->handle(Request::create($uri, $method, server: ['HTTP_ACCEPT' => 'application/json']));

        return $response->getStatusCode();
    }

    // ---------------------------------------------------------------- Ghi kết quả

    private function check(string $uat, string $name, callable $test): void
    {
        DB::statement('SAVEPOINT uat_step');

        try {
            $detail = (string) $test();
            $this->results[] = ['uat' => $uat, 'name' => $name, 'status' => 'ĐẠT', 'detail' => $detail];
            DB::statement('RELEASE SAVEPOINT uat_step');
        } catch (Throwable $e) {
            $this->results[] = ['uat' => $uat, 'name' => $name, 'status' => 'LỖI', 'detail' => $e->getMessage().' ('.class_basename($e).' '.basename($e->getFile()).':'.$e->getLine().')'];
            DB::statement('ROLLBACK TO SAVEPOINT uat_step');
        }

        $last = end($this->results);
        $this->line(sprintf('[%s] %s — %s: %s', $last['status'], $uat, $name, mb_strimwidth($last['detail'], 0, 160, '…')));
    }

    private function skip(string $uat, string $name, string $why): void
    {
        $this->results[] = ['uat' => $uat, 'name' => $name, 'status' => 'BỎ QUA', 'detail' => $why];
        $this->line("[BỎ QUA] {$uat} — {$name}: {$why}");
    }

    private function assert(bool $condition, string $message): void
    {
        if (! $condition) {
            throw new \RuntimeException($message);
        }
    }

    /** Thao tác phải bị từ chối (lỗi nghiệp vụ / dữ liệu / quyền); trả về thông điệp từ chối. */
    private function expectRejected(callable $action): string
    {
        try {
            $action();
        } catch (BusinessRuleException|ValidationException $e) {
            return $e->getMessage();
        } catch (HttpExceptionInterface $e) {
            return "HTTP {$e->getStatusCode()}: {$e->getMessage()}";
        }

        throw new \RuntimeException('Thao tác lẽ ra bị từ chối nhưng đã thành công.');
    }

    private function report(): string
    {
        $lines = ['# Nghiệm thu P1 (UAT-01 → UAT-08)', '', 'Chạy lúc '.now(config('studentmanager.display_timezone'))->format('H:i d/m/Y')
            .' trên CSDL `'.DB::getDatabaseName().'` bằng `php artisan uat:p1` (mọi thay đổi đã hoàn tác).', '',
            '| UAT | Kiểm tra | Kết quả | Chi tiết |', '|---|---|---|---|'];

        foreach ($this->results as $r) {
            $lines[] = '| '.$r['uat'].' | '.$r['name'].' | '.$r['status'].' | '.str_replace(['|', "\n"], ['\\|', ' '], $r['detail']).' |';
        }

        return implode("\n", $lines)."\n";
    }
}
