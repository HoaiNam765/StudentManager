<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Thời khóa biểu cơ bản (TTB, FR-TTB-001, 002):
 * - `periods`: khung tiết học trong ngày, dùng chung (`campus_id` trống) hoặc riêng một cơ sở. Ca (sáng / chiều / tối)
 *   ngăn cách bởi giờ nghỉ: một buổi học nằm gọn trong một ca (BR-TTB-03).
 * - `schedule_rules`: mẫu lịch của lớp học phần — thứ (ISO 1 = Thứ Hai … 7 = Chủ nhật), tiết từ – đến, phòng,
 *   các tuần học (từ tuần – đến tuần, tất cả / tuần lẻ / tuần chẵn; tuần 1 là tuần chứa ngày bắt đầu học kỳ).
 *   Buổi học cụ thể theo ngày (FR-TTB-005), kiểm tra trùng phòng / giảng viên (FR-TTB-003) ở các issue sau.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_id')->nullable()->constrained()->restrictOnDelete()->comment('Trống = dùng chung mọi cơ sở');
            $table->unsignedTinyInteger('number')->comment('Tiết thứ mấy trong ngày');
            $table->time('starts_at');
            $table->time('ends_at');
            $table->string('shift', 20)->comment('morning | afternoon | evening');
            $table->standardColumns();

            $table->index(['campus_id', 'number']);
        });

        Schema::create('schedule_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('day_of_week')->comment('ISO: 1 = Thứ Hai … 7 = Chủ nhật');
            $table->unsignedTinyInteger('period_from');
            $table->unsignedTinyInteger('period_to');
            $table->foreignId('room_id')->nullable()->constrained()->restrictOnDelete()->comment('Trống khi học trực tuyến');
            $table->unsignedTinyInteger('week_from');
            $table->unsignedTinyInteger('week_to');
            $table->string('week_parity', 10)->default('all')->comment('all | odd | even');
            $table->string('note', 500)->nullable();
            $table->standardColumns();

            $table->index(['course_section_id', 'day_of_week']);
            $table->index(['room_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_rules');
        Schema::dropIfExists('periods');
    }
};
