<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Hồ sơ sinh viên (FR-STU-001, 004, 005, 013; BR-STU-02, 03, 06, 07, 11; NFR-SEC-06).
 *
 * - `students`: số giấy tờ (CCCD hoặc hộ chiếu) lưu **mã hóa** (`national_id`) kèm mã băm HMAC (`national_id_hash`,
 *   unique) để kiểm tra trùng mà không cần giải mã (BR-STU-02). Email trường unique. MSSV, email trường, tài khoản cấp khi
 *   xác nhận nhập học (#102) nên để trống lúc tạo. CTĐT gán ở #97, lớp hành chính ở #99.
 * - `student_contacts`: người thân, liên hệ khẩn cấp (FR-STU-013).
 * - `student_change_logs`: lịch sử thay đổi từng trường (giá trị trước – sau, người sửa, thời điểm, lý do; FR-STU-005).
 *   Giá trị của trường nhạy cảm (CCCD) chỉ lưu dạng đã che.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_code', 20)->nullable()->unique()->comment('MSSV, cấp khi xác nhận nhập học (BR-STU-01)');
            $table->foreignId('user_id')->nullable()->unique()->constrained()->restrictOnDelete()->comment('Tài khoản (cấp khi nhập học)');
            $table->string('full_name');
            $table->foreignId('gender_id')->nullable()->constrained('lookup_values')->restrictOnDelete();
            $table->date('date_of_birth');
            $table->string('place_of_birth')->nullable();
            $table->foreignId('ethnicity_id')->nullable()->constrained('lookup_values')->restrictOnDelete();
            $table->foreignId('religion_id')->nullable()->constrained('lookup_values')->restrictOnDelete();
            $table->foreignId('nationality_id')->nullable()->constrained('lookup_values')->restrictOnDelete();

            $table->string('id_type', 20)->default('national_id')->comment('national_id (CCCD) | passport');
            $table->text('national_id')->nullable()->comment('Số CCCD / hộ chiếu, mã hóa (NFR-SEC-06)');
            $table->string('national_id_hash', 64)->nullable()->unique()->comment('HMAC của số giấy tờ để kiểm tra trùng (BR-STU-02)');
            $table->date('national_id_issued_on')->nullable();
            $table->string('national_id_issued_by')->nullable();

            $table->string('school_email')->nullable()->unique()->comment('Email trường (BR-STU-02)');
            $table->string('personal_email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('permanent_address', 500)->nullable()->comment('Số nhà, đường (dữ liệu nhạy cảm)');
            $table->foreignId('permanent_unit_id')->nullable()->constrained('administrative_units')->restrictOnDelete()->comment('Xã / phường thường trú');
            $table->string('temporary_address', 500)->nullable();
            $table->foreignId('temporary_unit_id')->nullable()->constrained('administrative_units')->restrictOnDelete();

            $table->foreignId('major_id')->constrained()->restrictOnDelete();
            $table->foreignId('specialization_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('training_type_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('cohort')->comment('Khóa (năm bắt đầu), ví dụ 2026');
            $table->foreignId('curriculum_id')->nullable()->constrained('curricula')->restrictOnDelete()->comment('CTĐT (#97)');
            $table->date('admission_date')->nullable()->comment('Ngày nhập học');
            $table->foreignId('priority_group_id')->nullable()->constrained('lookup_values')->restrictOnDelete()->comment('Đối tượng ưu tiên');
            $table->foreignId('priority_area_id')->nullable()->constrained('lookup_values')->restrictOnDelete()->comment('Khu vực ưu tiên');
            $table->string('photo_path')->nullable();
            $table->string('status', 20)->default('pending')->comment('pending | studying | suspended | withdrawn | expelled | transferred | graduated');
            $table->string('note', 1000)->nullable();
            $table->searchText();
            $table->standardColumns();

            $table->index(['major_id', 'cohort', 'status']);
            $table->index(['status', 'cohort']);
        });

        Schema::create('student_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->string('full_name');
            $table->string('relationship', 50)->comment('Cha, mẹ, người giám hộ…');
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_emergency')->default(false);
            $table->boolean('receives_notifications')->default(false)->comment('Đồng ý nhận thông báo');
            $table->standardColumns();
        });

        Schema::create('student_change_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->string('field', 50);
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->boolean('sensitive')->default(false);
            $table->string('reason', 500)->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('changed_at')->useCurrent();

            $table->index(['student_id', 'changed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_change_logs');
        Schema::dropIfExists('student_contacts');
        Schema::dropIfExists('students');
    }
};
