<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Thông báo trong ứng dụng (FR-NOT-001; BR-NOT-01, 06, 09):
 * - `notifications`: nội dung một lần gửi (tự động theo sự kiện hoặc thủ công). `event_key` duy nhất để mỗi thay đổi
 *   trạng thái chỉ sinh thông báo một lần. Hết hạn (`expires_at`) thì ẩn khỏi trung tâm thông báo nhưng vẫn lưu.
 * - `notification_recipients`: mỗi người nhận một dòng (đã đọc lúc nào), người dùng chỉ thấy dòng của mình.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('source', 16)->default('system')->comment('event | manual | system');
            $table->string('event_code', 20)->nullable()->comment('Mã sự kiện phụ lục A (EV-…)');
            $table->string('event_key', 191)->nullable()->unique()->comment('Khóa chống trùng (BR-NOT-09)');
            $table->string('category', 30)->default('general');
            $table->string('title', 200);
            $table->text('body');
            $table->string('link', 500)->nullable()->comment('Đường dẫn tương đối tới đối tượng liên quan');
            $table->string('priority', 10)->default('normal')->comment('normal | urgent');
            $table->boolean('mandatory')->default(false)->comment('Loại bắt buộc, không cho tắt (BR-NOT-02)');
            $table->boolean('pinned')->default(false);
            $table->string('status', 16)->default('sent')->comment('draft | scheduled | sent | revoked');
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->foreignId('revoked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('revoke_reason', 500)->nullable();
            $table->unsignedInteger('recipient_count')->default(0);
            $table->standardColumns();

            $table->index(['status', 'sent_at']);
        });

        Schema::create('notification_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->unique(['notification_id', 'user_id']);
            $table->index(['user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_recipients');
        Schema::dropIfExists('notifications');
    }
};
