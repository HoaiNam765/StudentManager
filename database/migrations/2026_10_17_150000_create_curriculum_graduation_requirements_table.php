<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Điều kiện tốt nghiệp của CTĐT và phiên bản CTĐT (FR-CUR-004, 005; BR-CUR-04, 05).
 *
 * - `curricula.effective_from`: ngày phiên bản bắt đầu hiệu lực; cùng `cohort_from`/`cohort_to` xác định khóa áp dụng.
 * - `curriculum_graduation_requirements`: mỗi phiên bản CTĐT một dòng, được chép sang phiên bản mới. CGPA trống thì lấy
 *   theo bộ quy chế của khóa (`graduation.min_cgpa`, mặc định 2,00).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('curricula', function (Blueprint $table) {
            $table->date('effective_from')->nullable()->after('cohort_to')->comment('Ngày phiên bản bắt đầu hiệu lực');
        });

        Schema::create('curriculum_graduation_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_id')->unique()->constrained('curricula')->restrictOnDelete();
            $table->unsignedSmallInteger('min_credits')->nullable()->comment('Trống = tổng tín chỉ của CTĐT');
            $table->decimal('min_cgpa', 3, 2)->nullable()->comment('Trống = theo bộ quy chế của khóa');
            $table->string('foreign_language_standard')->nullable()->comment('Chuẩn đầu ra ngoại ngữ, ví dụ bậc 3/6');
            $table->string('it_standard')->nullable()->comment('Chuẩn đầu ra tin học');
            $table->boolean('requires_internship')->default(false);
            $table->boolean('requires_thesis')->default(false);
            $table->boolean('allows_thesis_alternative')->default(false)->comment('Được học học phần thay thế khóa luận');
            $table->boolean('no_active_suspension')->default(true)->comment('Không đang bị kỷ luật đình chỉ học tập');
            $table->text('notes')->nullable();
            $table->standardColumns();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_graduation_requirements');

        Schema::table('curricula', function (Blueprint $table) {
            $table->dropColumn('effective_from');
        });
    }
};
