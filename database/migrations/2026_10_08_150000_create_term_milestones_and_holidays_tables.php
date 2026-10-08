<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lịch học vụ và ngày nghỉ (FR-ACY-004, 005, 007; BR-ACY-02, 05).
 *
 * term_milestones: mỗi học kỳ một mốc cho mỗi loại (mở/đóng đăng ký, bắt đầu học, hạn hủy, hạn rút, thi, nhập điểm,
 *   công bố điểm, nộp học phí, kết thúc kỳ); TTB, ENR, EXM, GRD, FEE đọc các mốc này.
 * milestone_change_requests: sửa mốc của học kỳ đã bắt đầu phải có lý do và người duyệt (BR-ACY-05).
 * holidays: nghỉ lễ, nghỉ hè, nghỉ trường; TTB dùng để loại trừ buổi học.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('term_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->string('type', 30);
            $table->date('date');
            $table->string('note', 500)->nullable();
            $table->standardColumns();

            $table->unique(['term_id', 'type']);
        });

        Schema::create('milestone_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->json('changes')->comment('{loại mốc: {old, new}}; new = null là bỏ mốc');
            $table->string('reason', 1000);
            $table->string('status', 20)->default('pending')->index()->comment('pending | approved | rejected');
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->string('decision_note', 1000)->nullable();
            $table->standardColumns();
        });

        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type', 20)->comment('public_holiday | summer_break | school_break');
            $table->date('starts_on');
            $table->date('ends_on');
            $table->foreignId('term_id')->nullable()->constrained()->nullOnDelete()->comment('Trống = áp dụng toàn trường, mọi học kỳ');
            $table->string('note', 500)->nullable();
            $table->standardColumns();

            $table->index(['starts_on', 'ends_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('milestone_change_requests');
        Schema::dropIfExists('term_milestones');
    }
};
