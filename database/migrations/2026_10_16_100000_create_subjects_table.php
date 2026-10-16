<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Danh mục học phần dùng chung (FR-SUB-001, 002, 003; BR-SUB-01, 02, 06): gốc của CTĐT, mở lớp, đăng ký và tính điểm.
 *
 * - Mã học phần là khóa nghiệp vụ: unique tính cả bản ghi đã xóa mềm, không đổi khi đã có dữ liệu liên quan.
 * - Học phần do đúng một bộ môn quản lý (khóa ngoại bắt buộc, không xóa bộ môn khi còn học phần).
 * - Tín chỉ là số nguyên không âm, lý thuyết + thực hành = tổng (kiểm tra cả ở CSDL lẫn SubjectService).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->foreignId('department_id')->constrained()->restrictOnDelete()->comment('Bộ môn quản lý');
            $table->text('description')->nullable();
            $table->string('teaching_language', 10)->default('vi')->comment('vi, en, vi_en (song ngữ)');

            $table->unsignedTinyInteger('credits')->comment('Tổng tín chỉ; 0 chỉ cho học phần điều kiện');
            $table->unsignedTinyInteger('theory_credits')->default(0);
            $table->unsignedTinyInteger('practice_credits')->default(0);
            $table->unsignedSmallInteger('theory_periods')->default(0)->comment('Số tiết lý thuyết');
            $table->unsignedSmallInteger('practice_periods')->default(0)->comment('Số tiết thực hành, thí nghiệm');
            $table->unsignedSmallInteger('self_study_hours')->default(0)->comment('Giờ tự học');

            $table->string('requirement', 20)->default('compulsory')->comment('compulsory | elective');
            $table->string('knowledge_block', 20)->comment('general | foundation | specialized | internship | thesis');
            $table->boolean('counts_toward_gpa')->default(true)->comment('Tính vào điểm trung bình (GPA)');
            $table->boolean('is_graduation_requirement')->default(false)->comment('Điều kiện tốt nghiệp (GDTC, GDQP-AN…), phải Đạt');

            $table->activeStatus();
            $table->searchText();
            $table->standardColumns();

            $table->index(['department_id', 'status']);
            $table->index(['knowledge_block', 'requirement']);
        });

        // BR-SUB-02 ở mức CSDL (MySQL 8 kiểm tra CHECK)
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            Schema::getConnection()->statement(
                'ALTER TABLE subjects ADD CONSTRAINT subjects_credits_sum_check CHECK (theory_credits + practice_credits = credits)'
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
