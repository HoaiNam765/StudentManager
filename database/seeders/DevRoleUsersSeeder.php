<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Auth\Enums\ProfileType;
use App\Modules\Auth\Models\Role;
use App\Modules\Faculty\Models\Faculty;
use App\Modules\Faculty\Models\LeadershipTerm;
use App\Modules\Faculty\Services\LeadershipService;
use Illuminate\Database\Seeder;

/**
 * Tài khoản mẫu cho từng vai trò, chỉ dùng ở môi trường phát triển (BR-SYS-10). Mật khẩu giả: xem hằng PASSWORD.
 * Tên đăng nhập `<vai trò viết thường>.test`, ví dụ `acad.test`, `stu.test`. Tài khoản DEAN được giao nhiệm kỳ
 * trưởng khoa CNTT (nếu có dữ liệu khoa) để có phạm vi FACULTY thật. Chạy lặp lại an toàn.
 */
class DevRoleUsersSeeder extends Seeder
{
    public const PASSWORD = 'password';

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
