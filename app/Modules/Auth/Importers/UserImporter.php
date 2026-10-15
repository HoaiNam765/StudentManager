<?php

namespace App\Modules\Auth\Importers;

use App\Models\User;
use App\Modules\Auth\Enums\ProfileType;
use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Services\UserService;
use App\Modules\System\Importers\SpreadsheetImporter;
use Illuminate\Support\Facades\DB;

/**
 * Tạo tài khoản hàng loạt từ danh sách (FR-AUTH-009) qua trung tâm import: xem trước, báo lỗi từng dòng, chọn lưu,
 * hoàn tác theo lô (GC-07). Mỗi tài khoản được cấp mật khẩu tạm gửi qua email, buộc đổi ở lần đầu (BR-AUTH-10).
 * Cột: username, name, email, profile_type (student | teacher | staff), roles (mã vai trò cách nhau bằng dấu phẩy, tùy chọn).
 */
class UserImporter extends SpreadsheetImporter
{
    public const KEY = 'users';

    public function key(): string
    {
        return self::KEY;
    }

    public function label(): string
    {
        return 'Tài khoản người dùng (tạo hàng loạt)';
    }

    public function description(): string
    {
        return 'Cột: username, name, email, profile_type (student | teacher | staff), roles (ví dụ "ACAD,ADV"; trống thì dùng vai trò mặc định). Mật khẩu tạm gửi qua email từng người.';
    }

    protected function columns(): array
    {
        return ['required' => ['username', 'name', 'email', 'profile_type'], 'optional' => ['roles']];
    }

    public function validateRow(int $rowNumber, array $rawRow): array
    {
        $errors = [];
        $username = $rawRow['username'] ?? '';
        $email = $rawRow['email'] ?? '';
        $profile = ProfileType::tryFrom($rawRow['profile_type'] ?? '');

        if (preg_match('/^[A-Za-z0-9._\-]{1,50}$/', $username) !== 1) {
            $errors[] = ['column' => 'username', 'message' => 'Tên đăng nhập bắt buộc, tối đa 50 ký tự, chỉ gồm chữ không dấu, số, dấu chấm, gạch ngang, gạch dưới.'];
        } elseif (User::query()->where('username', $username)->exists()) {
            $errors[] = ['column' => 'username', 'message' => "Tên đăng nhập {$username} đã được dùng (không tái sử dụng)."];
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = ['column' => 'email', 'message' => 'Email không hợp lệ.'];
        } elseif (User::query()->where('email', $email)->exists()) {
            $errors[] = ['column' => 'email', 'message' => "Email {$email} đã gắn với tài khoản khác."];
        }

        if (($rawRow['name'] ?? '') === '') {
            $errors[] = ['column' => 'name', 'message' => 'Họ và tên bắt buộc.'];
        }

        if ($profile === null) {
            $errors[] = ['column' => 'profile_type', 'message' => 'Loại hồ sơ phải là student, teacher hoặc staff.'];
        }

        $roles = $this->roles($rawRow['roles'] ?? '');
        $unknown = array_diff($roles, Role::query()->whereIn('code', $roles)->active()->pluck('code')->all());

        if ($unknown !== []) {
            $errors[] = ['column' => 'roles', 'message' => 'Không có vai trò đang hoạt động: '.implode(', ', $unknown).'.'];
        } elseif ($profile === ProfileType::Staff && $roles === []) {
            $errors[] = ['column' => 'roles', 'message' => 'Tài khoản cán bộ, nhân viên cần ít nhất một vai trò.'];
        }

        return $errors;
    }

    public function saveRow(int $rowNumber, array $rawRow): array
    {
        $user = app(UserService::class)->create([
            'username' => $rawRow['username'],
            'name' => $rawRow['name'],
            'email' => $rawRow['email'],
            'profile_type' => $rawRow['profile_type'],
            'roles' => $this->roles($rawRow['roles'] ?? ''),
        ]);

        return ['type' => 'user', 'id' => $user->id];
    }

    /** Chỉ hoàn tác được tài khoản chưa từng đăng nhập và chưa gắn hồ sơ. */
    public function canRollbackRow(string $modelType, int|string $modelId): bool
    {
        $user = User::query()->find($modelId);

        return $user === null || ($user->last_login_at === null && $user->profile_id === null);
    }

    public function rollbackRow(string $modelType, int|string $modelId): bool
    {
        $user = User::query()->find($modelId);

        if ($user === null) {
            return true;
        }

        // Tài khoản do chính lô này tạo và chưa dùng: xóa hẳn (gồm vai trò, lịch sử mật khẩu)
        DB::table('user_roles')->where('user_id', $user->id)->delete();
        $user->passwordHistories()->delete();

        return (bool) $user->delete();
    }

    /** @return list<string> */
    private function roles(string $value): array
    {
        return array_values(array_filter(array_map(fn (string $code) => strtoupper(trim($code)), explode(',', $value))));
    }
}
