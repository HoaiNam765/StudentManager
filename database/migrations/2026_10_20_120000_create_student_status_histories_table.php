<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lịch sử trạng thái sinh viên (FR-STU-010): mỗi lần chuyển theo quyết định — trạng thái cũ / mới, số quyết định, ngày
 * hiệu lực, lý do, tệp quyết định (disk riêng tư), người thực hiện (`created_by`). `special` = khôi phục từ trạng thái
 * cuối bằng quyết định đặc biệt của ACAD (BR-STU-05). Chỉ ghi thêm, không sửa.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->string('from_status', 20);
            $table->string('to_status', 20);
            $table->string('decision_no', 100);
            $table->date('effective_date');
            $table->string('reason', 1000);
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->boolean('special')->default(false);
            $table->standardColumns();

            $table->index(['student_id', 'effective_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_status_histories');
    }
};
