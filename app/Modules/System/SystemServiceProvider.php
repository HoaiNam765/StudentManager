<?php

namespace App\Modules\System;

use App\Modules\System\Importers\AdministrativeUnitImporter;
use App\Modules\System\Importers\LookupValueImporter;
use App\Modules\System\Models\AdministrativeUnit;
use App\Modules\System\Models\LookupCategory;
use App\Modules\System\Models\LookupValue;
use App\Modules\System\Policies\LookupPolicy;
use App\Modules\System\Services\ImportRegistry;
use App\Support\References\ReferenceRegistry;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Đăng ký của module System (SYS): Policy, Importer của danh mục dùng chung và các tham chiếu nội bộ.
 * Module khác tham chiếu danh mục (ví dụ students.gender_id) thì tự đăng ký ở ServiceProvider của module đó.
 */
class SystemServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(LookupCategory::class, LookupPolicy::class);
        Gate::policy(LookupValue::class, LookupPolicy::class);
        Gate::policy(AdministrativeUnit::class, LookupPolicy::class);

        $imports = $this->app->make(ImportRegistry::class);
        $imports->registerLazy(LookupValueImporter::KEY, fn () => new LookupValueImporter);
        $imports->registerLazy(AdministrativeUnitImporter::KEY, fn () => new AdministrativeUnitImporter);

        // Đơn vị hành chính cũ trỏ tới đơn vị mới thay thế: đơn vị mới đang được dữ liệu cũ tham chiếu
        $this->app->make(ReferenceRegistry::class)
            ->register(AdministrativeUnit::class, 'administrative_units', 'successor_id', 'đơn vị cũ được thay thế');
    }
}
