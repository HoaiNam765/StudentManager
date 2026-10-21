<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Điều chỉnh điểm sau công bố và trạng thái điểm đặc biệt (FR-GRD-006, 012; BR-GRD-06, 07):
 * - `grade_change_requests`: giảng viên đề nghị (lý do, minh chứng, điểm mới từng thành phần), khoa duyệt (nếu bật) rồi
 *   Phòng Đào tạo / Khảo thí xác nhận.
 * - `grade_versions`: ảnh chụp trước – sau của mỗi lần điều chỉnh, bất biến.
 * - `course_results.special_status`: vắng thi, hoãn thi, cấm thi, miễn, công nhận… (ký hiệu và cách tính GPA ở
 *   `config/grade_special_statuses.php`).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->restrictOnDelete();
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('faculty_approved_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('status', 24)->default('pending_faculty')->comment('pending_faculty | pending_confirmation | approved | rejected');
            $table->text('reason');
            $table->string('evidence_path')->nullable();
            $table->json('proposed_scores');
            $table->text('rejection_reason')->nullable();
            $table->standardColumns();
            $table->index(['enrollment_id', 'status']);
        });

        Schema::create('grade_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->restrictOnDelete();
            $table->foreignId('grade_change_request_id')->nullable()->constrained()->restrictOnDelete();
            $table->unsignedInteger('version');
            $table->json('snapshot');
            $table->string('reason', 2000);
            $table->standardColumns();
            $table->unique(['enrollment_id', 'version']);
        });

        Schema::table('course_results', function (Blueprint $table) {
            $table->string('special_status', 24)->nullable()->after('status')->comment('Trạng thái điểm đặc biệt (FR-GRD-012)');
            $table->string('special_reason', 500)->nullable()->after('special_status');
        });
    }

    public function down(): void
    {
        Schema::table('course_results', function (Blueprint $table) {
            $table->dropColumn(['special_status', 'special_reason']);
        });
        Schema::dropIfExists('grade_versions');
        Schema::dropIfExists('grade_change_requests');
    }
};
