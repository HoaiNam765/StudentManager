<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kết quả học phần của từng đăng ký (BR-GRD-01..03, 11): điểm thang 10 đã làm tròn, điểm chữ, điểm hệ 4, đạt / không đạt,
 * trạng thái (`incomplete` khi thiếu điểm thành phần bắt buộc). Thang quy đổi và điểm đạt lấy từ bộ quy chế theo khóa
 * (`grading.scale`, `grading.pass_score` qua PolicyResolver), không lưu bảng thang riêng.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')
                ->unique()
                ->constrained()
                ->restrictOnDelete();
            $table->decimal('score10', 4, 2)->nullable();
            $table->string('letter', 3)->nullable();
            $table->decimal('point4', 3, 1)->nullable();
            $table->boolean('is_passed')->nullable();
            $table->string('status', 30)->default('incomplete');
            $table->standardColumns();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_results');
    }
};
