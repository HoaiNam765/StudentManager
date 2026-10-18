<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Đề cương chi tiết học phần có phiên bản (FR-SUB-006; BR-SUB-08; NFR-SEC-07).
 *
 * Mỗi học phần có nhiều phiên bản đề cương (`version` tăng dần); vòng đời: nháp → chờ duyệt → đã duyệt
 * (hoặc bị trả lại → sửa → gửi lại); bản duyệt mới thay bản duyệt cũ (superseded). Tệp PDF lưu ở disk riêng tư, chỉ
 * tải qua API có kiểm tra quyền.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('syllabi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('version');
            $table->text('objectives')->nullable()->comment('Mục tiêu');
            $table->json('learning_outcomes')->nullable()->comment('[{code, description}]');
            $table->json('content')->nullable()->comment('[{unit (tuần / chương), topic, hours}]');
            $table->text('teaching_methods')->nullable();
            $table->text('assessment')->nullable()->comment('Mô tả đánh giá; cơ cấu điểm chính thức ở grade_component_templates');
            $table->json('materials')->nullable()->comment('[{title, kind: main | reference}]');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->string('status', 20)->default('draft')->comment('draft | submitted | approved | rejected | superseded');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->string('decision_note', 500)->nullable();
            $table->standardColumns();

            $table->unique(['subject_id', 'version']);
            $table->index(['subject_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('syllabi');
    }
};
