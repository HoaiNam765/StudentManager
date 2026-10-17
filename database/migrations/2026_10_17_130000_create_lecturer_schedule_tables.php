<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lịch bận, nguyện vọng giảng dạy và nghỉ phép của giảng viên (FR-TCH-008, 012; FR-TTB-010).
 *
 * - `lecturer_availabilities`: khung giờ bận lặp lại hằng tuần (thứ, tiết từ – đến) trong một khoảng ngày.
 * - `teaching_preferences`: học phần giảng viên muốn dạy ở một học kỳ sắp tới, có thứ tự ưu tiên.
 * - `lecturer_leaves`: nghỉ phép, ốm, công tác theo khoảng ngày, chờ cán bộ khác duyệt; đã duyệt thì TTB xử lý buổi dạy.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lecturer_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('day_of_week')->comment('1 = Thứ Hai, 7 = Chủ nhật');
            $table->unsignedTinyInteger('period_from');
            $table->unsignedTinyInteger('period_to');
            $table->date('starts_on');
            $table->date('ends_on');
            $table->string('reason', 255)->nullable();
            $table->standardColumns();

            $table->index(['lecturer_id', 'day_of_week']);
        });

        Schema::create('teaching_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->constrained()->restrictOnDelete();
            $table->foreignId('term_id')->constrained()->restrictOnDelete();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('priority')->default(1)->comment('1 = ưu tiên cao nhất');
            $table->string('note', 255)->nullable();
            $table->standardColumns();

            $table->index(['term_id', 'subject_id']);
            $table->index(['lecturer_id', 'term_id']);
        });

        Schema::create('lecturer_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->constrained()->restrictOnDelete();
            $table->string('type', 20)->comment('annual | sick | business_trip | other');
            $table->date('starts_on');
            $table->date('ends_on');
            $table->string('reason', 500);
            $table->string('status', 20)->default('pending')->comment('pending | approved | rejected | cancelled');
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('decided_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->string('decision_note', 500)->nullable();
            $table->standardColumns();

            $table->index(['lecturer_id', 'status', 'starts_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lecturer_leaves');
        Schema::dropIfExists('teaching_preferences');
        Schema::dropIfExists('lecturer_availabilities');
    }
};
