<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Thông báo thủ công (FR-NOT-006, 015; BR-NOT-05, 08):
 * - `notifications` thêm đối tượng nhận đã chọn (`audience`), giờ hẹn gửi, tệp đính kèm (disk riêng tư).
 * - `recipient_groups`: nhóm người nhận người gửi lưu lại để dùng lại (lưu bộ chọn, khi gửi mới tính ra người nhận và
 *   kiểm tra lại phạm vi).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->json('audience')->nullable()->after('sender_id')->comment('Đối tượng nhận đã chọn (thông báo thủ công)');
            $table->timestamp('scheduled_at')->nullable()->after('audience')->comment('Hẹn giờ gửi (UTC)');
            $table->string('attachment_path')->nullable()->after('link');
            $table->string('attachment_name')->nullable()->after('attachment_path');
            $table->index(['status', 'scheduled_at']);
        });

        Schema::create('recipient_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name', 150);
            $table->json('audience');
            $table->standardColumns();

            $table->index('owner_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipient_groups');

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['status', 'scheduled_at']);
            $table->dropColumn(['audience', 'scheduled_at', 'attachment_path', 'attachment_name']);
        });
    }
};
