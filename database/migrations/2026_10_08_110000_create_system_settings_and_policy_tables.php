<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tham số hệ thống (FR-SYS-001) và bộ quy chế đào tạo theo khóa (FR-SYS-003, BR-SYS-02, GC-12).
 *
 * system_settings: mỗi tham số một dòng; khóa chưa có dòng nào thì dùng giá trị mặc định trong code.
 * policy_sets / policy_items: bộ quy chế có phiên bản, phạm vi khóa và ngày hiệu lực. Bộ đã ban hành
 * không sửa được (không hồi tố); muốn đổi thì tạo phiên bản mới có hiệu lực từ một ngày sau.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->json('value')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('policy_sets', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->comment('Mã bộ quy chế, ví dụ QC-TT56; các phiên bản dùng chung mã');
            $table->unsignedSmallInteger('version');
            $table->string('name');
            $table->string('description', 1000)->nullable();
            $table->unsignedSmallInteger('cohort_from')->nullable()->comment('Khóa (năm nhập học) đầu tiên áp dụng; trống = không giới hạn dưới');
            $table->unsignedSmallInteger('cohort_to')->nullable()->comment('Khóa cuối cùng áp dụng; trống = không giới hạn trên');
            $table->date('effective_from')->comment('Ngày bắt đầu áp dụng');
            $table->string('status', 20)->default('draft')->index()->comment('draft | published');
            $table->foreignId('based_on_id')->nullable()->constrained('policy_sets')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->standardColumns();

            $table->unique(['code', 'version']);
            $table->index(['status', 'effective_from']);
        });

        Schema::create('policy_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policy_set_id')->constrained()->cascadeOnDelete();
            $table->string('key', 100);
            $table->json('value');
            $table->timestamps();

            $table->unique(['policy_set_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policy_items');
        Schema::dropIfExists('policy_sets');
        Schema::dropIfExists('system_settings');
    }
};
