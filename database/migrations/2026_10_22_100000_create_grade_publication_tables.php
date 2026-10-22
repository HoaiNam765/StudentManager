<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Quy trình duyệt và công bố điểm theo lớp học phần (FR-GRD-004, 005): Nháp → Đã nộp → (khoa duyệt nếu bật) → Đã xác
 * nhận → Đã công bố → Đã khóa; trả lại kèm lý do; hẹn lịch công bố. Kết quả học phần dùng chung bảng `course_results`
 * (#120), không tạo lại ở đây.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section_grade_workflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')->unique()->constrained()->restrictOnDelete();
            $table->string('status', 24)->default('draft')->comment('draft | submitted | faculty_approved | confirmed | published | locked | returned');
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('return_reason')->nullable();
            $table->timestamp('scheduled_at')->nullable()->comment('Hẹn giờ công bố (UTC)');
            $table->timestamp('published_at')->nullable();
            $table->standardColumns();

            $table->index(['status', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_grade_workflows');
    }
};
