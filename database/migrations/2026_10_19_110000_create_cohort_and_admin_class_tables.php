<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Khóa tuyển sinh và lớp hành chính (FR-CLS-001, 002; BR-CLS-01).
 *
 * - `cohorts`: mã khóa (ví dụ K2026) và năm bắt đầu duy nhất; năm kết thúc dự kiến; bộ quy chế áp dụng (trống = theo
 *   `PolicyResolver` cho năm bắt đầu của khóa).
 * - `admin_classes`: mã lớp duy nhất (tính cả lớp đã xóa mềm); mỗi lớp đúng một khóa và một ngành (khóa ngoại bắt buộc),
 *   chuyên ngành tùy chọn, hệ đào tạo, sĩ số tối đa, trạng thái hoạt động / đã đóng.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cohorts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('Mã khóa, ví dụ K2026');
            $table->string('name')->nullable();
            $table->unsignedSmallInteger('start_year')->unique();
            $table->unsignedSmallInteger('expected_end_year');
            $table->foreignId('policy_set_id')->nullable()->constrained()->restrictOnDelete()->comment('Bộ quy chế áp dụng; trống = tự chọn theo năm');
            $table->activeStatus();
            $table->searchText();
            $table->standardColumns();
        });

        Schema::create('admin_classes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique()->comment('Mã lớp hành chính (BR-CLS-01)');
            $table->string('name');
            $table->foreignId('cohort_id')->constrained()->restrictOnDelete();
            $table->foreignId('major_id')->constrained()->restrictOnDelete();
            $table->foreignId('specialization_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('training_type_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('max_size');
            $table->activeStatus();
            $table->searchText();
            $table->standardColumns();

            $table->index(['cohort_id', 'major_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_classes');
        Schema::dropIfExists('cohorts');
    }
};
