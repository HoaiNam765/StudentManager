<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lịch sử gán CTĐT cho sinh viên (FR-CUR-006; BR-CUR-01, BR-STU-04). Mỗi thời điểm một CTĐT hiệu lực; `students.curriculum_id`
 * giữ CTĐT hiện hành để đọc nhanh, bảng này giữ lịch sử với ngày hiệu lực (chuyển ngành, đổi CTĐT).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_curricula', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('curriculum_id')->constrained('curricula')->restrictOnDelete();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->string('reason', 500)->nullable();
            $table->standardColumns();

            $table->index(['student_id', 'effective_from']);
            $table->index('curriculum_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_curricula');
    }
};
