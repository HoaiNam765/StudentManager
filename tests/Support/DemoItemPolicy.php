<?php

namespace Tests\Support;

use App\Support\Policies\DenyByDefaultPolicy;
use Illuminate\Contracts\Auth\Authenticatable;

/** Ví dụ Policy của một module: chỉ mở những quyền cần thiết, còn lại mặc định từ chối. */
class DemoItemPolicy extends DenyByDefaultPolicy
{
    public function viewAny(?Authenticatable $user): bool
    {
        return $user !== null;
    }
}
