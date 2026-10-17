<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cơ cấu điểm mặc định của học phần (FR-SUB-005; BR-SUB-04): các thành phần điểm, trọng số (%), điểm sàn, bắt buộc.
 * Lớp học phần (GRD) sao chép cơ cấu này khi mở lớp. Cả cơ cấu được lưu (thay thế) một lần qua GradeStructureService.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_component_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->string('name', 100);
            $table->string('kind', 20)->comment('attendance | assignment | midterm | practical | final_exam | other');
            $table->decimal('weight', 5, 2)->comment('Trọng số (%)');
            $table->decimal('min_score', 4, 2)->nullable()->comment('Điểm sàn (thang 10)');
            $table->boolean('is_required')->default(true)->comment('Bắt buộc có điểm / đạt điểm sàn');
            $table->boolean('is_remote')->default(false)->comment('Đánh giá từ xa (trực tuyến)');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->standardColumns();

            $table->index(['subject_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_component_templates');
    }
};
