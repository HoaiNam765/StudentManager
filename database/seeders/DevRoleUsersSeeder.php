<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Auth\Enums\ProfileType;
use App\Modules\Auth\Models\Role;
use App\Modules\Faculty\Models\Department;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\Faculty\Models\LeadershipTerm;
use App\Modules\Faculty\Services\LeadershipService;
use App\Modules\SchoolClass\Models\AdminClass;
use App\Modules\SchoolClass\Models\AdvisorAssignment;
use App\Modules\SchoolClass\Services\AdvisorAssignmentService;
use App\Modules\Student\Enums\StudentStatus;
use App\Modules\Student\Models\Student;
use App\Modules\Teacher\Models\Lecturer;
use App\Modules\Teacher\Services\LecturerService;
use Illuminate\Database\Seeder;

/**
 * Tài khoản mẫu cho từng vai trò, chỉ dùng ở môi trường phát triển (BR-SYS-10). Mật khẩu giả: xem hằng PASSWORD.
 * Tên đăng nhập `<vai trò viết thường>.test`, ví dụ `acad.test`, `stu.test`. Tài khoản DEAN được giao nhiệm kỳ
 * trưởng khoa CNTT (nếu có dữ liệu khoa) để có phạm vi FACULTY thật; tài khoản ADV được gắn hồ sơ giảng viên ADVISOR_CODE
 * và cố vấn một lớp hành chính để có phạm vi ADVISEE thật. Tài khoản của sinh viên đã nhập học cũng dùng mật khẩu giả
 * này. Chạy lặp lại an toàn; không chạy ở production.
 */
class DevRoleUsersSeeder extends Seeder
{
    public const PASSWORD = 'password';

    /** Mã hồ sơ giảng viên của tài khoản cố vấn mẫu `adv.test`. */
    public const ADVISOR_CODE = 'GV9001';

    /** Vai trò => [họ tên, loại hồ sơ] */
    public const ACCOUNTS = [
        'ACAD' => ['Cán bộ Đào tạo thử nghiệm', ProfileType::Staff],
        'EXAM' => ['Cán bộ Khảo thí thử nghiệm', ProfileType::Staff],
        'CTSV' => ['Cán bộ Công tác sinh viên thử nghiệm', ProfileType::Staff],
        'FIN' => ['Cán bộ Tài chính thử nghiệm', ProfileType::Staff],
        'DEAN' => ['Trưởng khoa thử nghiệm', ProfileType::Teacher],
        'LEC' => ['Giảng viên thử nghiệm', ProfileType::Teacher],
        'ADV' => ['Cố vấn học tập thử nghiệm', ProfileType::Teacher],
        'STU' => ['Sinh viên thử nghiệm', ProfileType::Student],
    ];

    public function run(): void
    {
        if (app()->isProduction()) {
            return;
        }

        $today = now(config('studentmanager.display_timezone'))->toDateString();

        foreach (self::ACCOUNTS as $code => [$name, $profile]) {
            $username = strtolower($code).'.test';
            $user = User::query()->where('username', $username)->first();

            if ($user === null) {
                $user = new User;
                $user->forceFill([
                    'username' => $username,
                    'name' => $name,
                    'email' => strtolower($code).'@studentmanager.test',
                    'password' => self::PASSWORD,
                    'profile_type' => $profile,
                ])->save();
            }

            if ($code === 'DEAN') {
                $this->makeDean($user, $today);

                continue;
            }

            $roles = array_unique(array_filter([$code, $profile->defaultRole()]));

            foreach (Role::query()->whereIn('code', $roles)->get() as $role) {
                if (! $user->roles()->whereKey($role->id)->exists()) {
                    $user->roles()->attach($role->id, ['valid_from' => $today]);
                }
            }

            if ($code === 'ADV') {
                $this->makeAdvisor($user, $today);
            }
        }

        $this->prepareAdmittedStudents();
    }

    /**
     * Sinh viên đã nhập học (Đang học, có tài khoản) nhận mật khẩu tạm ngẫu nhiên và phải đổi ở lần đầu; để dùng thử cổng
     * sinh viên, đặt mật khẩu giả PASSWORD (đăng nhập bằng MSSV). Chỉ áp cho tài khoản còn mật khẩu tạm, nên không ghi đè
     * mật khẩu người dùng đã tự đổi. `stu.test` vẫn không gắn hồ sơ sinh viên để thử trường hợp đó.
     */
    private function prepareAdmittedStudents(): void
    {
        $userIds = Student::query()->where('status', StudentStatus::Studying)->whereNotNull('user_id')->pluck('user_id');

        User::query()->whereIn('id', $userIds)->where('must_change_password', true)->get()
            ->each(fn (User $user) => $user->forceFill([
                'password' => self::PASSWORD,
                'must_change_password' => false,
                'temporary_password_expires_at' => null,
                'password_changed_at' => now(),
            ])->save());
    }

    /**
     * Cố vấn học tập: cổng giảng viên cần hồ sơ giảng viên gắn với tài khoản (`lecturers.user_id`), phạm vi ADVISEE cần một
     * phân công cố vấn đang hiệu lực. Gắn hồ sơ ADVISOR_CODE (tạo nếu chưa có) và cố vấn lớp hành chính đầu tiên (theo mã)
     * chưa có cố vấn. Chưa có bộ môn / lớp hoặc mã đã dùng cho hồ sơ khác thì bỏ qua phần tương ứng.
     */
    private function makeAdvisor(User $user, string $today): void
    {
        $lecturer = Lecturer::query()->where('user_id', $user->id)->first();

        if ($lecturer === null) {
            $department = Department::query()->active()->where('code', 'BM-KTPM')->first() ?? Department::query()->active()->orderBy('code')->first();

            if ($department === null || Lecturer::withTrashed()->where('code', self::ADVISOR_CODE)->exists()) {
                return;
            }

            $lecturer = app(LecturerService::class)->create([
                'code' => self::ADVISOR_CODE,
                'full_name' => $user->name,
                'email' => $user->email,
                'department_id' => $department->id,
                'hired_on' => '2015-09-01',
                'account' => 'link',
                'user_id' => $user->id,
            ]);
        }

        if (! $lecturer->isAssignable() || AdvisorAssignment::query()->where('lecturer_id', $lecturer->id)->current()->exists()) {
            return;
        }

        $class = AdminClass::query()->active()
            ->whereDoesntHave('advisorAssignments', fn ($q) => $q->where(fn ($period) => $period->whereNull('effective_to')->orWhereDate('effective_to', '>=', $today)))
            ->orderBy('code')->first();

        if ($class !== null) {
            app(AdvisorAssignmentService::class)->assign($class, $lecturer, $today, 'Tài khoản mẫu');
        }
    }

    /** Trưởng khoa: giao nhiệm kỳ (sinh vai trò DEAN có phạm vi khoa); chưa có khoa thì gán DEAN trực tiếp. */
    private function makeDean(User $user, string $today): void
    {
        $lecturer = Role::query()->where('code', 'LEC')->first();

        if ($lecturer !== null && ! $user->roles()->whereKey($lecturer->id)->exists()) {
            $user->roles()->attach($lecturer->id, ['valid_from' => $today]);
        }

        $faculty = Faculty::query()->where('code', 'CNTT')->first();

        if ($faculty !== null) {
            if (! LeadershipTerm::query()->where('user_id', $user->id)->exists()
                && ! LeadershipTerm::query()->forUnit('faculty', $faculty->id)->where('position', 'dean')->effectiveOn($today)->exists()) {
                app(LeadershipService::class)->assign([
                    'unit_type' => 'faculty', 'unit_id' => $faculty->id, 'user_id' => $user->id,
                    'position' => 'dean', 'starts_on' => $today, 'note' => 'Tài khoản mẫu',
                ]);
            }

            return;
        }

        $dean = Role::query()->where('code', 'DEAN')->first();

        if ($dean !== null && ! $user->roles()->whereKey($dean->id)->exists()) {
            $user->roles()->attach($dean->id, ['valid_from' => $today]);
        }
    }
}
