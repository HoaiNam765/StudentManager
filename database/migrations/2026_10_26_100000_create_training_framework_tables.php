<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Chương trình khung theo khoa – khóa – học kỳ: danh sách học phần Phòng Đào tạo dự kiến mở cho sinh viên một khóa của
 * một khoa trong một học kỳ, kèm số lớp dự kiến; dùng để mở lớp học phần hàng loạt và thông báo cho sinh viên.
 *
 * - `training_frameworks`: một khung cho mỗi (khoa, khóa, học kỳ); `notified_at` lần gửi thông báo gần nhất.
 * - `training_framework_items`: học phần trong khung (bắt buộc / tự chọn), số lớp dự kiến, sĩ số tối đa (trống = mặc định).
 * - `course_sections.training_framework_item_id`: lớp được mở từ mục nào của khung (đếm số lớp đã mở, không mở trùng).
 * Xóa khung / mục là xóa hẳn (chỉ khi chưa mở lớp nào) để có thể tạo lại cùng khoa – khóa – học kỳ.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_frameworks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained()->restrictOnDelete();
            $table->foreignId('cohort_id')->constrained()->restrictOnDelete();
            $table->foreignId('term_id')->constrained()->restrictOnDelete();
            $table->string('note', 1000)->nullable();
            $table->timestamp('notified_at')->nullable()->comment('Lần gửi thông báo cho sinh viên gần nhất (UTC)');
            $table->standardColumns();

            $table->unique(['faculty_id', 'cohort_id', 'term_id']);
            $table->index(['term_id', 'cohort_id']);
        });

        Schema::create('training_framework_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_framework_id')->constrained()->restrictOnDelete();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->boolean('compulsory')->default(true);
            $table->unsignedSmallInteger('planned_sections')->default(1)->comment('Số lớp dự kiến mở');
            $table->unsignedSmallInteger('max_size')->nullable()->comment('Sĩ số tối đa mỗi lớp; trống = cấu hình mặc định');
            $table->string('note', 500)->nullable();
            $table->standardColumns();

            $table->unique(['training_framework_id', 'subject_id']);
        });

        Schema::table('course_sections', function (Blueprint $table) {
            $table->foreignId('training_framework_item_id')->nullable()->after('term_id')
                ->constrained()->nullOnDelete()->comment('Mở từ chương trình khung');
        });
    }

    public function down(): void
    {
        Schema::table('course_sections', function (Blueprint $table) {
            $table->dropConstrainedForeignId('training_framework_item_id');
        });
        Schema::dropIfExists('training_framework_items');
        Schema::dropIfExists('training_frameworks');
    }
};
