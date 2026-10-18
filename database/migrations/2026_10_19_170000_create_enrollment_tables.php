<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Đăng ký học phần (ENR, FR-ENR-003..008, 017):
 * - `enrollments`: sinh viên – lớp học phần, loại (thường / học lại / cải thiện), trạng thái (đã đăng ký → đã chốt →
 *   hoàn thành; hủy; rút), thời điểm theo giờ máy chủ (BR-ENR-11), nguồn (sinh viên / cán bộ), lý do ngoại lệ.
 *   `active_section_id` (cột sinh) = lớp khi đăng ký còn hiệu lực, NULL khi đã hủy / rút: unique (sinh viên, lớp) chặn
 *   đăng ký trùng ngay ở CSDL kể cả khi hai yêu cầu đến cùng lúc.
 * - `enrollment_logs`: mỗi lần thử (thành công / bị từ chối kèm lý do, IP) để tra cứu khiếu nại (FR-ENR-017); chỉ ghi thêm.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('course_section_id')->constrained()->restrictOnDelete();
            $table->foreignId('term_id')->constrained()->restrictOnDelete();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->foreignId('registration_period_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 20)->default('normal')->comment('normal | retake | improve');
            $table->string('status', 20)->default('registered')->comment('registered | confirmed | completed | cancelled | withdrawn');
            $table->string('source', 20)->default('student')->comment('student | staff');
            $table->timestamp('registered_at');
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->string('override_reason', 500)->nullable()->comment('Lý do ngoại lệ khi cán bộ bỏ qua điều kiện (FR-ENR-009)');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->standardColumns();

            $table->index(['course_section_id', 'status']);
            $table->index(['student_id', 'term_id', 'status']);
        });

        DB::statement("ALTER TABLE enrollments ADD active_section_id BIGINT UNSIGNED GENERATED ALWAYS AS (CASE WHEN status IN ('registered','confirmed','completed') AND deleted_at IS NULL THEN course_section_id END) STORED");
        DB::statement('ALTER TABLE enrollments ADD UNIQUE enrollments_student_active_section_unique (student_id, active_section_id)');

        Schema::create('enrollment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('course_section_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 20)->comment('register | cancel | withdraw | change');
            $table->boolean('success');
            $table->json('reasons')->nullable();
            $table->string('ip', 45)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('created_at');

            $table->index(['student_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_logs');
        Schema::dropIfExists('enrollments');
    }
};
