<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Điểm danh (ATT, BA 6.8; #142, #143, #144):
 * - `attendance_records`: một sinh viên (đăng ký học phần) trong một buổi học: có mặt / vắng không phép / vắng có phép /
 *   đi muộn (FR-ATT-002). Duy nhất theo buổi – đăng ký.
 * - `attendance_change_requests`: sửa sau khung thời gian cho phép phải được Phòng Đào tạo duyệt (FR-ATT-005).
 * - `attendance_summaries`: số buổi vắng, tỷ lệ vắng, cờ cảnh báo / cấm thi của từng đăng ký (FR-ATT-008, 009).
 * - `attendance_locks`: khóa sổ điểm danh của lớp học phần cuối kỳ (FR-ATT-013, BR-ATT-07).
 * - `teaching_logs`: sổ đầu bài — số tiết thực dạy, nội dung buổi (FR-ATT-012).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_session_id')->constrained()->restrictOnDelete();
            $table->foreignId('enrollment_id')->constrained()->restrictOnDelete();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('course_section_id')->constrained()->restrictOnDelete();
            $table->string('status', 20)->comment('present | absent | excused | late');
            $table->unsignedSmallInteger('minutes_late')->nullable();
            $table->string('note', 500)->nullable();
            $table->string('change_reason', 500)->nullable()->comment('Lý do lần sửa gần nhất (giá trị cũ – mới ở nhật ký kiểm toán)');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('recorded_at')->nullable();
            $table->standardColumns();
            $table->unique(['class_session_id', 'enrollment_id']);
            $table->index(['course_section_id', 'student_id']);
        });

        Schema::create('attendance_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_record_id')->constrained()->restrictOnDelete();
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->string('from_status', 20);
            $table->string('to_status', 20);
            $table->unsignedSmallInteger('minutes_late')->nullable();
            $table->string('reason', 1000);
            $table->string('status', 20)->default('pending')->comment('pending | approved | rejected');
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->string('decision_note', 1000)->nullable();
            $table->standardColumns();
            $table->index(['status', 'attendance_record_id']);
        });

        Schema::create('attendance_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('course_section_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('total_sessions')->default(0)->comment('Buổi dự kiến có học (không gồm buổi nghỉ / hủy, BR-ATT-06)');
            $table->unsignedSmallInteger('recorded_sessions')->default(0);
            $table->unsignedSmallInteger('present_count')->default(0);
            $table->unsignedSmallInteger('absent_count')->default(0);
            $table->unsignedSmallInteger('excused_count')->default(0);
            $table->unsignedSmallInteger('late_count')->default(0);
            $table->decimal('absences', 6, 2)->default(0)->comment('Số buổi vắng quy đổi (BR-ATT-02, 04)');
            $table->decimal('absence_rate', 5, 2)->default(0)->comment('% trên tổng số buổi dự kiến');
            $table->boolean('warning')->default(false);
            $table->boolean('banned')->default(false)->comment('Không đủ điều kiện dự thi (BR-ATT-03)');
            $table->boolean('exempted')->default(false)->comment('Ngoại lệ ACAD / EXAM xét, không bị cấm thi');
            $table->string('exemption_reason', 500)->nullable();
            $table->decimal('attendance_score', 4, 1)->nullable()->comment('Điểm chuyên cần hệ 10 (FR-ATT-008)');
            $table->timestamp('warned_at')->nullable();
            $table->timestamp('banned_at')->nullable();
            $table->timestamp('calculated_at')->nullable();
            $table->standardColumns();
            $table->index(['course_section_id', 'banned']);
            $table->index(['student_id', 'warning']);
        });

        Schema::create('attendance_locks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')->unique()->constrained()->restrictOnDelete();
            $table->boolean('locked')->default(true);
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('locked_at')->nullable();
            $table->string('unlock_reason', 500)->nullable();
            $table->standardColumns();
        });

        Schema::create('teaching_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_session_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('lecturer_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('periods')->comment('Số tiết thực dạy');
            $table->text('content')->comment('Nội dung buổi dạy');
            $table->timestamp('confirmed_at');
            $table->standardColumns();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teaching_logs');
        Schema::dropIfExists('attendance_locks');
        Schema::dropIfExists('attendance_summaries');
        Schema::dropIfExists('attendance_change_requests');
        Schema::dropIfExists('attendance_records');
    }
};
