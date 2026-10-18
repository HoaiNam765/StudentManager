<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Chương trình đào tạo (FR-CUR-001, 002, 003; BR-CUR-02, 03, 06, 07).
 *
 * - `curricula`: CTĐT của một ngành (có thể theo chuyên ngành), hệ đào tạo, áp dụng cho các khóa từ `cohort_from`
 *   đến `cohort_to`; yêu cầu tổng tín chỉ và thời gian đào tạo chuẩn. `version`, `based_on_id` dành cho phiên bản (#86).
 * - `curriculum_blocks`: khối kiến thức của CTĐT và số tín chỉ yêu cầu.
 * - `curriculum_elective_groups`: nhóm tự chọn "chọn tối thiểu x tín chỉ", thuộc một khối.
 * - `curriculum_subjects`: học phần của CTĐT; mỗi học phần chỉ một lần trong cùng CTĐT (unique).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curricula', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30);
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('based_on_id')->nullable()->constrained('curricula')->restrictOnDelete();
            $table->string('name');
            $table->foreignId('major_id')->constrained()->restrictOnDelete();
            $table->foreignId('specialization_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('training_type_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('cohort_from')->comment('Khóa áp dụng từ (năm nhập học)');
            $table->unsignedSmallInteger('cohort_to')->nullable()->comment('Khóa áp dụng đến; trống = chưa giới hạn');
            $table->unsignedSmallInteger('total_credits')->comment('Tổng tín chỉ yêu cầu');
            $table->unsignedTinyInteger('standard_terms')->comment('Thời gian đào tạo chuẩn (số học kỳ), khớp với ngành');
            $table->text('description')->nullable();
            $table->string('status', 20)->default('draft')->comment('draft | published | retired');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->searchText();
            $table->standardColumns();

            $table->unique(['code', 'version']);
            $table->index(['major_id', 'status']);
        });

        Schema::create('curriculum_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_id')->constrained('curricula')->restrictOnDelete();
            $table->string('name');
            $table->string('block_type', 20)->comment('general | foundation | specialized | internship | thesis');
            $table->unsignedSmallInteger('required_credits');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->standardColumns();
        });

        Schema::create('curriculum_elective_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_id')->constrained('curricula')->restrictOnDelete();
            $table->foreignId('curriculum_block_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('min_credits')->comment('Chọn tối thiểu x tín chỉ trong nhóm');
            $table->standardColumns();
        });

        Schema::create('curriculum_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_id')->constrained('curricula')->restrictOnDelete();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->foreignId('curriculum_block_id')->constrained()->restrictOnDelete();
            $table->foreignId('curriculum_elective_group_id')->nullable()->constrained()->restrictOnDelete()->comment('Trống = bắt buộc');
            $table->unsignedTinyInteger('planned_term')->comment('Học kỳ dự kiến (1..số học kỳ chuẩn)');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('outside_prerequisites_confirmed')->default(false)->comment('Tiên quyết ngoài CTĐT đã được xác nhận (BR-CUR-03)');
            $table->standardColumns();

            // Mỗi học phần chỉ xuất hiện một lần trong cùng CTĐT (BR-CUR-03). Xóa khỏi CTĐT là xóa hẳn (không xóa mềm).
            $table->unique(['curriculum_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_subjects');
        Schema::dropIfExists('curriculum_elective_groups');
        Schema::dropIfExists('curriculum_blocks');
        Schema::dropIfExists('curricula');
    }
};
