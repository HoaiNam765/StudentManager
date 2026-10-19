<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Yêu cầu sửa trường nhạy cảm của sinh viên (FR-STU-006; BR-STU-06): họ tên, ngày sinh, số CCCD, ngành chỉ đổi qua
 * yêu cầu kèm minh chứng, cán bộ khác duyệt / từ chối có lý do. `changes` = {trường: {old, new}}, mã hóa cả cột vì có thể
 * chứa CCCD. Mỗi sinh viên tối đa một yêu cầu đang chờ.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->text('changes')->comment('JSON {trường: {old, new}}, mã hóa vì có thể chứa CCCD');
            $table->string('reason', 1000);
            $table->string('evidence_path');
            $table->string('evidence_name')->nullable();
            $table->string('status', 20)->default('pending')->index()->comment('pending | approved | rejected | cancelled');
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->string('decision_note', 1000)->nullable();
            $table->standardColumns();

            $table->index(['student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_change_requests');
    }
};
