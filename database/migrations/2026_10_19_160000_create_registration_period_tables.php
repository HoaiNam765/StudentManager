<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Đợt đăng ký học phần (ENR, FR-ENR-001):
 * - `registration_periods`: học kỳ, vòng (vòng 1, vòng 2, bổ sung), cửa sổ thời gian lưu UTC theo giờ máy chủ
 *   (BR-ENR-11; mặc định lấy từ mốc Mở / Đóng đăng ký của lịch học vụ ACY), khung tín chỉ riêng của đợt (trống = theo
 *   quy chế, BR-ENR-02 kiểm tra ở #104), trạng thái nháp → công bố → đã chốt.
 * - `registration_period_targets`: đối tượng của đợt; mỗi dòng là một tổ hợp khóa / ngành / lớp hành chính (cột trống =
 *   không giới hạn theo tiêu chí đó). Đợt không có dòng nào áp dụng cho mọi sinh viên Đang học (BR-ENR-01).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('round', 20)->comment('round_1 | round_2 | supplementary');
            $table->timestamp('opens_at')->comment('UTC');
            $table->timestamp('closes_at')->comment('UTC');
            $table->unsignedTinyInteger('min_credits')->nullable()->comment('Trống = theo quy chế (BR-ENR-02)');
            $table->unsignedTinyInteger('max_credits')->nullable()->comment('Trống = theo quy chế (BR-ENR-02)');
            $table->string('status', 20)->default('draft')->index()->comment('draft | published | finalized');
            $table->standardColumns();

            $table->index(['term_id', 'opens_at']);
        });

        Schema::create('registration_period_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cohort_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('major_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('admin_class_id')->nullable()->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_period_targets');
        Schema::dropIfExists('registration_periods');
    }
};
