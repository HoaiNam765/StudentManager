<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cơ cấu tổ chức đào tạo (FR-FAC-001..005; BR-FAC-01, 02, 05, 07): khoa → bộ môn, khoa → ngành → chuyên ngành,
 * và danh mục hệ đào tạo. Là gốc của phạm vi dữ liệu FACULTY và tham chiếu của STU, TCH, SUB, CUR, CLS.
 *
 * Bộ môn thuộc đúng một khoa, ngành thuộc đúng một khoa quản lý, chuyên ngành thuộc đúng một ngành (khóa ngoại bắt buộc).
 * Mã là khóa nghiệp vụ: unique tính cả bản ghi đã xóa mềm.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->date('founded_on')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('description')->nullable();
            $table->activeStatus();
            $table->searchText();
            $table->standardColumns();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained()->restrictOnDelete();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->activeStatus();
            $table->searchText();
            $table->standardColumns();

            $table->index(['faculty_id', 'status']);
        });

        Schema::create('training_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('description', 500)->nullable();
            $table->boolean('is_default')->default(false)->comment('Hệ mặc định khi tạo hồ sơ (chính quy)');
            $table->activeStatus();
            $table->standardColumns();
        });

        Schema::create('majors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained()->restrictOnDelete()->comment('Khoa quản lý ngành');
            $table->string('code', 20)->unique()->comment('Mã ngành, ví dụ 7480201');
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('education_level', 20)->default('undergraduate')->comment('undergraduate | master | doctorate');
            $table->unsignedSmallInteger('total_credits')->comment('Tổng tín chỉ chuẩn');
            $table->unsignedTinyInteger('standard_terms')->comment('Thời gian đào tạo chuẩn (số học kỳ), căn cứ BR-FAC-07');
            $table->activeStatus();
            $table->searchText();
            $table->standardColumns();

            $table->index(['faculty_id', 'status']);
        });

        Schema::create('specializations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('major_id')->constrained()->restrictOnDelete();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->activeStatus();
            $table->searchText();
            $table->standardColumns();

            $table->index(['major_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specializations');
        Schema::dropIfExists('majors');
        Schema::dropIfExists('training_types');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('faculties');
    }
};
