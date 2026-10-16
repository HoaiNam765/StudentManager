<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Hồ sơ giảng viên (FR-TCH-001, 003; BR-TCH-01, 02, 06, 07; BR-FAC-03).
 *
 * - `lecturers`: mã giảng viên duy nhất (tính cả bản ghi đã xóa mềm); mỗi hồ sơ gắn tối đa một tài khoản
 *   (`user_id` unique, có thể trống); đúng một đơn vị chính là bộ môn (khóa ngoại bắt buộc).
 * - `lecturer_units`: đơn vị kiêm nhiệm (bộ môn hoặc khoa) có ngày hiệu lực; lịch sử được giữ, kết thúc bằng `ends_on`.
 * - `lecturer_change_requests`: giảng viên đề nghị đổi họ tên, chức danh, học vị; chờ cán bộ có thẩm quyền xác nhận.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lecturers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('Mã giảng viên, cũng là tên đăng nhập');
            $table->foreignId('user_id')->nullable()->unique()->constrained()->restrictOnDelete()->comment('Tài khoản đăng nhập (tối đa một)');
            $table->string('full_name');
            $table->date('date_of_birth')->nullable();
            $table->foreignId('gender_id')->nullable()->constrained('lookup_values')->restrictOnDelete();
            $table->string('national_id', 20)->nullable()->unique()->comment('CCCD, dữ liệu nhạy cảm');
            $table->string('email')->nullable()->comment('Email công vụ');
            $table->string('phone', 30)->nullable();
            $table->string('photo_path')->nullable();
            $table->foreignId('department_id')->constrained()->restrictOnDelete()->comment('Đơn vị chính (bộ môn)');
            $table->string('academic_title', 30)->default('lecturer')->comment('Chức danh');
            $table->string('academic_degree', 30)->nullable()->comment('Học vị');
            $table->string('expertise', 500)->nullable()->comment('Chuyên môn');
            $table->date('hired_on')->nullable();
            $table->string('status', 20)->default('working')->comment('working | resigned | retired');
            $table->date('left_on')->nullable()->comment('Ngày nghỉ việc / nghỉ hưu');
            $table->string('left_reason', 500)->nullable();
            $table->searchText();
            $table->standardColumns();

            $table->index(['department_id', 'status']);
        });

        Schema::create('lecturer_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->constrained()->restrictOnDelete();
            $table->string('unit_type', 20)->comment('faculty | department');
            $table->unsignedBigInteger('unit_id');
            $table->string('note', 500)->nullable()->comment('Nội dung kiêm nhiệm');
            $table->date('starts_on');
            $table->date('ends_on')->nullable();
            $table->standardColumns();

            $table->index(['unit_type', 'unit_id', 'starts_on']);
            $table->index(['lecturer_id', 'starts_on']);
        });

        Schema::create('lecturer_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->constrained()->restrictOnDelete();
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->json('changes')->comment('{trường: {old, new}}');
            $table->string('reason', 500)->nullable();
            $table->string('status', 20)->default('pending')->comment('pending | approved | rejected | cancelled');
            $table->foreignId('decided_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->string('decision_note', 500)->nullable();
            $table->standardColumns();

            $table->index(['lecturer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lecturer_change_requests');
        Schema::dropIfExists('lecturer_units');
        Schema::dropIfExists('lecturers');
    }
};
