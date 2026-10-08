<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Danh mục dùng chung (FR-SYS-002, BR-SYS-09, BR-STU-09).
 *
 * lookup_categories / lookup_values: giới tính, dân tộc, tôn giáo, quốc tịch, đối tượng ưu tiên, loại hợp đồng…
 * administrative_units: đơn vị hành chính theo mô hình hai cấp từ 01/07/2025 (tỉnh – xã) và dữ liệu ba cấp cũ
 * (tỉnh – huyện – xã) để tra cứu địa chỉ đã nhập trước đó.
 *
 * Mã là khóa nghiệp vụ ổn định: unique tính cả bản ghi đã xóa mềm, nên mã đã dùng không bao giờ được cấp lại.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lookup_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique()->comment('Mã danh mục, ví dụ GENDER, ETHNICITY');
            $table->string('name');
            $table->string('description', 500)->nullable();
            $table->boolean('is_system')->default(false)->comment('Danh mục hệ thống: không xóa được');
            $table->activeStatus();
            $table->searchText();
            $table->standardColumns();
        });

        Schema::create('lookup_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lookup_category_id')->constrained()->restrictOnDelete();
            $table->string('code', 30)->comment('Mã ổn định trong danh mục; không đổi sau khi tạo');
            $table->string('name');
            $table->unsignedInteger('sort_order')->default(0);
            $table->activeStatus();
            $table->searchText();
            $table->standardColumns();

            $table->unique(['lookup_category_id', 'code']);
            $table->index(['lookup_category_id', 'status', 'sort_order']);
        });

        Schema::create('administrative_units', function (Blueprint $table) {
            $table->id();
            $table->string('scheme', 30)->comment('two_level_2025 (từ 01/07/2025) | three_level_legacy (dữ liệu cũ)');
            $table->string('level', 20)->comment('province | district | commune');
            $table->string('code', 10)->comment('Mã đơn vị hành chính, duy nhất trong cùng mô hình');
            $table->string('name');
            $table->string('unit_type', 50)->comment('Tỉnh, Thành phố, Phường, Xã, Đặc khu, Quận, Huyện…');
            $table->foreignId('parent_id')->nullable()->constrained('administrative_units')->restrictOnDelete();
            $table->foreignId('successor_id')->nullable()->constrained('administrative_units')->nullOnDelete()
                ->comment('Dữ liệu cũ: đơn vị mới (mô hình hai cấp) thay thế');
            $table->activeStatus();
            $table->searchText();
            $table->standardColumns();

            $table->unique(['scheme', 'code']);
            $table->index(['scheme', 'level', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('administrative_units');
        Schema::dropIfExists('lookup_values');
        Schema::dropIfExists('lookup_categories');
    }
};
