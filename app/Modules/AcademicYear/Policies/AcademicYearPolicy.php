<?php

namespace App\Modules\AcademicYear\Policies;

use App\Support\Policies\DenyByDefaultPolicy;
use Illuminate\Contracts\Auth\Authenticatable;

class AcademicYearPolicy extends DenyByDefaultPolicy
{
    public function viewAny(?Authenticatable $user): bool
    {
        return $user !== null;
    }

    public function view(
        ?Authenticatable $user,
        $academicYear
    ): bool {
        return $user !== null;
    }

    public function create(?Authenticatable $user): bool
    {
        return false;
    }

    public function update(
        ?Authenticatable $user,
        $academicYear
    ): bool {
        return false;
    }

    public function delete(
        ?Authenticatable $user,
        $academicYear
    ): bool {
        return false;
    }
}
