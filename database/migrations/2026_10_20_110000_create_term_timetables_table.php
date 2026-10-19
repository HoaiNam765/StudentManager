<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Trạng thái thời khóa biểu của một học kỳ (FR-TTB-006): nháp → công bố → khóa. Chưa có dòng = nháp.
 * Sau công bố mọi thay đổi lịch phải có lý do và báo trước `studentmanager.timetable.change_notice_hours` giờ
 * (khẩn cấp là ngoại lệ có ghi nhận, BR-TTB-05); đã khóa thì không đổi lịch.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('term_timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->unique()->constrained()->restrictOnDelete();
            $table->string('status', 20)->default('draft')->comment('draft | published | locked');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('locked_at')->nullable();
            $table->standardColumns();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('term_timetables');
    }
};
