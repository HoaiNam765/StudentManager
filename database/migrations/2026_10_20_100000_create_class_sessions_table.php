<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Buổi học cụ thể theo ngày (TTB, FR-TTB-005, 009; BR-TTB-06): sinh từ mẫu lịch `schedule_rules` và lịch học vụ.
 * Trạng thái: planned (dự kiến) | taught (đã dạy) | off (nghỉ — trùng ngày nghỉ, không tính vắng) | makeup (dạy bù,
 * `makeup_for_id` trỏ buổi nghỉ được bù) | cancelled (hủy). Sinh lại khi đổi lịch thì xóa mềm các buổi dự kiến / nghỉ
 * chưa diễn ra (giữ lịch sử), giữ nguyên buổi đã dạy, dạy bù, hủy. Là cơ sở điểm danh (ATT, P2).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')->constrained()->restrictOnDelete();
            $table->foreignId('schedule_rule_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date');
            $table->unsignedTinyInteger('week')->nullable()->comment('Tuần học thứ mấy của học kỳ');
            $table->unsignedTinyInteger('period_from');
            $table->unsignedTinyInteger('period_to');
            $table->foreignId('room_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('status', 20)->default('planned')->comment('planned | taught | off | makeup | cancelled');
            $table->string('off_reason', 255)->nullable();
            $table->foreignId('makeup_for_id')->nullable()->constrained('class_sessions')->nullOnDelete();
            $table->string('note', 500)->nullable();
            $table->standardColumns();

            $table->index(['course_section_id', 'date']);
            $table->index(['room_id', 'date']);
            $table->index(['date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_sessions');
    }
};
