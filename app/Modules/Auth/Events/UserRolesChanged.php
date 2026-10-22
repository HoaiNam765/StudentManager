<?php

namespace App\Modules\Auth\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

/** EV-AUTH-03 (phụ lục A): người dùng được gán (`assigned`) hoặc gỡ (`revoked`) một vai trò; NOT báo cho người đó. */
class UserRolesChanged
{
    use Dispatchable;

    public function __construct(
        public readonly User $user,
        public readonly string $change,
        public readonly string $roleName,
    ) {}
}
