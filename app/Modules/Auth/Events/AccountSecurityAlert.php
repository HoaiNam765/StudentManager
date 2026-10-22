<?php

namespace App\Modules\Auth\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * EV-AUTH-02 (phụ lục A): tài khoản bị khóa tạm (`locked`) hoặc vừa có đăng nhập bất thường (`unusual_login`). NOT báo
 * cho chủ tài khoản và ADMIN. `$key` phân biệt từng lần (thời điểm khóa / mã lần đăng nhập) để không báo trùng.
 */
class AccountSecurityAlert
{
    use Dispatchable;

    public function __construct(
        public readonly User $user,
        public readonly string $type,
        public readonly string $key,
    ) {}
}
