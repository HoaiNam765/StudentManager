<?php

namespace App\Support\Database;

use Illuminate\Database\Schema\Blueprint;

/**
 * Các lệnh viết tắt cho migration, khớp với các trait trong App\Support\Concerns.
 *
 *     Schema::create('faculties', function (Blueprint $table) {
 *         $table->id();
 *         $table->string('code', 20)->unique();
 *         $table->string('name');
 *         $table->activeStatus();     // HasActiveStatus
 *         $table->searchText();       // HasVietnameseSearch
 *         $table->standardColumns();  // người tạo/sửa, timestamps, xóa mềm (StandardModel)
 *     });
 */
final class BlueprintMacros
{
    public static function register(): void
    {
        Blueprint::macro('standardColumns', function (): void {
            /** @var Blueprint $this */
            $this->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $this->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $this->timestamps();
            $this->softDeletes();
        });

        Blueprint::macro('activeStatus', function (): void {
            /** @var Blueprint $this */
            $this->string('status', 20)->default('active')->index();
        });

        Blueprint::macro('effectivePeriod', function (): void {
            /** @var Blueprint $this */
            $this->date('effective_from');
            $this->date('effective_to')->nullable();
            $this->index(['effective_from', 'effective_to']);
        });

        Blueprint::macro('searchText', function (): void {
            /** @var Blueprint $this */
            $this->string('search_text', 500)->default('');
        });
    }
}
