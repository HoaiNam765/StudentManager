<?php

namespace App\Modules\Auth;

use App\Modules\Auth\Importers\UserImporter;
use App\Modules\System\Services\ImportRegistry;
use Illuminate\Support\ServiceProvider;

/**
 * Đăng ký của module Auth: Importer tạo tài khoản hàng loạt (FR-AUTH-009).
 * Các dịch vụ scoped của phân quyền (AccessControl, AuditLogger) vẫn ở AppServiceProvider như trước.
 */
class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->make(ImportRegistry::class)
            ->registerLazy(UserImporter::KEY, fn () => $this->app->make(UserImporter::class));
    }
}
