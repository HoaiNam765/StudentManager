<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Modules\Auth\Enums\AccountStatus;
use App\Modules\Auth\Enums\ProfileType;
use App\Modules\Auth\Enums\StudentAccountEvent;
use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Notifications\TemporaryPasswordIssued;
use App\Support\Audit\AuditEvent;
use App\Support\Audit\AuditLogger;
use App\Support\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Quản lý người dùng (FR-AUTH-008, 009; BR-AUTH-01, 05, 07, 08, 10).
 *
 * - Mỗi tài khoản gắn đúng một hồ sơ chính (sinh viên, giảng viên, nhân viên); một hồ sơ chỉ gắn một tài khoản.
 * - Tên đăng nhập duy nhất, không đổi và không tái sử dụng (tài khoản không bị xóa, chỉ ngừng).
 * - Tạo tài khoản: gán vai trò mặc định theo loại hồ sơ, cấp mật khẩu tạm buộc đổi ở lần đầu, gửi qua email.
 * - Khóa, ngừng: không được làm mất ADMIN cuối cùng (BR-AUTH-05); hủy mọi phiên đang mở.
 * - BR-AUTH-07: STU gọi `applyStudentStatus()`; thôi học / buộc thôi học / chuyển trường thì hẹn ngừng sau N ngày
 *   (cấu hình), tốt nghiệp thì chuyển chỉ đọc. Lệnh `users:apply-deactivations` chạy hằng ngày thực hiện lịch hẹn.
 */
class UserService extends BaseService
{
    public function __construct(
        private readonly AuditLogger $audit,
        private readonly RoleService $roles,
        private readonly PasswordService $passwords,
        private readonly LoginService $logins,
        private readonly UserSessions $sessions,
    ) {}

    /**
     * @param  array{username: string, name: string, email: string, profile_type: string, profile_id?: ?int, roles?: list<string>}  $data
     */
    public function create(array $data, ?User $actor = null): User
    {
        $profileType = ProfileType::from($data['profile_type']);
        $username = trim($data['username']);

        if (User::query()->where('username', $username)->exists()) {
            $this->fail("Tên đăng nhập {$username} đã được dùng.", 'Tên đăng nhập không được tái sử dụng (BR-AUTH-01); chọn tên khác (ví dụ MSSV hoặc mã cán bộ).');
        }

        if (User::query()->where('email', $data['email'])->exists()) {
            $this->fail("Email {$data['email']} đã gắn với một tài khoản khác.", 'Mỗi email chỉ dùng cho một tài khoản; kiểm tra lại hoặc dùng email khác.');
        }

        $this->assertProfileFree($profileType, $data['profile_id'] ?? null);

        $roleCodes = array_values(array_unique(array_filter([...($data['roles'] ?? []), $profileType->defaultRole()])));

        if ($roleCodes === []) {
            $this->fail('Tài khoản cán bộ, nhân viên cần ít nhất một vai trò.', 'Chọn vai trò theo phòng ban (ví dụ ACAD, FIN, CTSV).');
        }

        $roles = Role::query()->whereIn('code', $roleCodes)->get();
        $missing = array_diff($roleCodes, $roles->pluck('code')->all());

        if ($missing !== []) {
            $this->fail('Không có vai trò: '.implode(', ', $missing).'.', 'Xem danh sách vai trò ở /admin/roles.');
        }

        $temporary = self::temporaryPassword();

        $user = $this->transaction(function () use ($data, $username, $profileType, $roles, $temporary): User {
            $user = new User;
            $user->forceFill([
                'username' => $username,
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Str::random(40),
                'status' => AccountStatus::Active,
                'profile_type' => $profileType,
                'profile_id' => $data['profile_id'] ?? null,
            ])->save();

            $this->audit->record(AuditEvent::Created, $user, [], [
                'username' => $username,
                'email' => $data['email'],
                'profile_type' => $profileType->value,
                'profile_id' => $data['profile_id'] ?? null,
            ], 'Tạo tài khoản');

            $this->passwords->setTemporaryPassword($user, $temporary);

            foreach ($roles as $role) {
                $this->roles->assign($user, $role);
            }

            return $user;
        });

        $user->notify(new TemporaryPasswordIssued($temporary));

        return $user->refresh();
    }

    /** @param  array{name?: string, email?: string}  $data */
    public function update(User $user, array $data): User
    {
        if (isset($data['email']) && $data['email'] !== $user->email && User::query()->where('email', $data['email'])->whereKeyNot($user->id)->exists()) {
            $this->fail("Email {$data['email']} đã gắn với một tài khoản khác.", 'Mỗi email chỉ dùng cho một tài khoản.');
        }

        $old = $user->only(array_keys($data));
        $user->forceFill(array_intersect_key($data, array_flip(['name', 'email'])))->save();
        $this->audit->record(AuditEvent::Updated, $user, $old, $user->only(array_keys($data)), 'Sửa thông tin tài khoản');

        return $user->refresh();
    }

    /** Gắn tài khoản với hồ sơ (STU, TCH gọi khi tạo hồ sơ). Một hồ sơ chỉ gắn một tài khoản, tài khoản không đổi loại hồ sơ. */
    public function linkProfile(User $user, ProfileType $type, int $profileId): User
    {
        if ($user->profile_type !== null && $user->profile_type !== $type) {
            $this->fail(
                "Tài khoản {$user->username} đang gắn hồ sơ {$user->profile_type->label()}, không gắn thêm hồ sơ {$type->label()} được.",
                'Mỗi tài khoản gắn đúng một hồ sơ chính (BR-AUTH-01); tạo tài khoản riêng cho hồ sơ này.'
            );
        }

        $this->assertProfileFree($type, $profileId, $user);

        $user->forceFill(['profile_type' => $type, 'profile_id' => $profileId])->save();
        $this->audit->record(AuditEvent::Updated, $user, [], ['profile_type' => $type->value, 'profile_id' => $profileId], 'Gắn hồ sơ');

        return $user->refresh();
    }

    public function lock(User $user, string $reason, User $actor): User
    {
        $this->assertNotSelf($user, $actor, 'khóa');
        $this->roles->ensureNotLastAdmin($user);

        return $this->changeStatus($user, AccountStatus::Locked, $reason, AuditEvent::Locked, endSessions: true);
    }

    /** Mở khóa: bỏ trạng thái khóa hẳn (nếu có) và cả khóa tạm do nhập sai mật khẩu. */
    public function unlock(User $user, User $actor): User
    {
        if ($user->isLockedOut()) {
            $this->logins->unlock($user);
        }

        if ($user->status === AccountStatus::Locked) {
            return $this->changeStatus($user, AccountStatus::Active, 'Mở khóa tài khoản', AuditEvent::Unlocked);
        }

        return $user->refresh();
    }

    public function deactivate(User $user, string $reason, User $actor): User
    {
        $this->assertNotSelf($user, $actor, 'ngừng');
        $this->roles->ensureNotLastAdmin($user);

        return $this->changeStatus($user, AccountStatus::Inactive, $reason, AuditEvent::Updated, endSessions: true);
    }

    public function activate(User $user, User $actor): User
    {
        return $this->changeStatus($user, AccountStatus::Active, 'Kích hoạt lại tài khoản', AuditEvent::Updated);
    }

    /** Cấp lại mật khẩu tạm (quản trị viên), gửi qua email; mọi phiên đang mở bị hủy. */
    public function issueTemporaryPassword(User $user): void
    {
        if (! $user->canSignIn()) {
            $this->fail("Tài khoản {$user->username} đang {$user->status->label()}.", 'Mở khóa hoặc kích hoạt lại tài khoản trước khi cấp mật khẩu tạm.');
        }

        $temporary = self::temporaryPassword();
        $this->passwords->setTemporaryPassword($user, $temporary);
        $user->notify(new TemporaryPasswordIssued($temporary));
    }

    /** BR-AUTH-07: STU gọi khi đổi tình trạng học tập của sinh viên. */
    public function applyStudentStatus(User $user, StudentAccountEvent $event): User
    {
        if ($user->profile_type !== ProfileType::Student) {
            $this->fail("Tài khoản {$user->username} không phải tài khoản sinh viên.", 'Chỉ áp dụng cho tài khoản gắn hồ sơ sinh viên.');
        }

        return match ($event) {
            StudentAccountEvent::Dropped, StudentAccountEvent::Expelled, StudentAccountEvent::Transferred => $this->scheduleDeactivation($user, $event),
            StudentAccountEvent::Graduated => $this->changeStatus($user, AccountStatus::ReadOnly, $event->label().': chuyển sang chỉ đọc', AuditEvent::Updated, clearSchedule: true),
            StudentAccountEvent::Reinstated => $this->changeStatus($user, AccountStatus::Active, $event->label(), AuditEvent::Updated, clearSchedule: true),
        };
    }

    /** Thực hiện các lịch hẹn ngừng tài khoản đã đến hạn; trả về số tài khoản đã ngừng. */
    public function applyScheduledDeactivations(): int
    {
        $count = 0;

        User::query()
            ->whereNotNull('deactivate_at')
            ->where('deactivate_at', '<=', now())
            ->where('status', '!=', AccountStatus::Inactive->value)
            ->chunkById(100, function ($users) use (&$count): void {
                foreach ($users as $user) {
                    $this->changeStatus($user, AccountStatus::Inactive, $user->status_reason ?? 'Ngừng theo lịch hẹn', AuditEvent::Updated, endSessions: true, clearSchedule: true);
                    $count++;
                }
            });

        return $count;
    }

    /** Mật khẩu tạm ngẫu nhiên luôn đạt chính sách mặc định (có chữ hoa, chữ thường, số). */
    public static function temporaryPassword(): string
    {
        // Bỏ các ký tự dễ nhầm (O/0, I/l/1) vì người dùng phải gõ lại mật khẩu tạm từ email
        $upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lower = 'abcdefghijkmnpqrstuvwxyz';
        $digits = '23456789';
        $all = $upper.$lower.$digits;
        $pick = fn (string $set): string => $set[random_int(0, strlen($set) - 1)];

        $length = max(12, (int) config('studentmanager.auth.password.min_length'));
        $chars = [$pick($upper), $pick($lower), $pick($digits)];

        while (count($chars) < $length) {
            $chars[] = $pick($all);
        }

        if (config('studentmanager.auth.password.symbols')) {
            $chars[] = $pick('!@#$%&*?');
        }

        shuffle($chars);

        return implode('', $chars);
    }

    private function scheduleDeactivation(User $user, StudentAccountEvent $event): User
    {
        $days = (int) config('studentmanager.auth.student_deactivate_after_days');
        $at = now()->addDays($days);

        $user->forceFill(['deactivate_at' => $at, 'status_reason' => $event->label()])->save();
        $this->audit->record(AuditEvent::Updated, $user, [], ['deactivate_at' => $at->toIso8601String()], "{$event->label()}: hẹn ngừng tài khoản sau {$days} ngày (BR-AUTH-07)");

        if ($days <= 0) {
            $this->applyScheduledDeactivations();
        }

        return $user->refresh();
    }

    private function changeStatus(User $user, AccountStatus $status, string $reason, AuditEvent $event, bool $endSessions = false, bool $clearSchedule = false): User
    {
        $old = $user->status ?? AccountStatus::Active;

        DB::transaction(function () use ($user, $status, $reason, $event, $old, $clearSchedule): void {
            $user->forceFill([
                'status' => $status,
                'status_reason' => $reason,
                'status_changed_at' => now(),
                ...($clearSchedule || $status === AccountStatus::Active ? ['deactivate_at' => null] : []),
            ])->save();

            $this->audit->record($event, $user, ['status' => $old->value], ['status' => $status->value], $reason);
        });

        if ($endSessions) {
            $this->sessions->terminate($user);
        }

        return $user->refresh();
    }

    private function assertProfileFree(ProfileType $type, ?int $profileId, ?User $except = null): void
    {
        if ($profileId === null) {
            return;
        }

        $taken = User::query()
            ->where('profile_type', $type->value)
            ->where('profile_id', $profileId)
            ->when($except !== null, fn ($q) => $q->whereKeyNot($except->id))
            ->first();

        if ($taken !== null) {
            $this->fail(
                "Hồ sơ {$type->label()} #{$profileId} đã gắn với tài khoản {$taken->username}.",
                'Mỗi hồ sơ chỉ gắn một tài khoản (BR-AUTH-01).'
            );
        }
    }

    private function assertNotSelf(User $user, User $actor, string $action): void
    {
        if ($user->is($actor)) {
            $this->fail("Không tự {$action} tài khoản của chính mình.", 'Nhờ một quản trị viên khác thực hiện.');
        }
    }
}
