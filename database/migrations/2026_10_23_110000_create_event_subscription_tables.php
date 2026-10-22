<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Thông báo tự động theo sự kiện (FR-NOT-004, 007; BR-NOT-02):
 * - `event_subscriptions`: một dòng cho mỗi sự kiện của phụ lục A — người nhận, kênh, mẫu tiêu đề / nội dung / liên kết,
 *   bắt buộc (người dùng không tắt được), bật / tắt.
 * - `notification_preferences`: người dùng tắt nhận một loại thông báo không bắt buộc.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('event_code', 20)->unique();
            $table->string('name', 200);
            $table->string('module', 10);
            $table->string('category', 30)->default('general');
            $table->string('recipients', 200)->comment('Người nhận theo phụ lục A (mô tả)');
            $table->json('channels')->comment('in_app, email…; P1 chỉ gửi in_app');
            $table->string('title_template', 200);
            $table->text('body_template');
            $table->string('link_template', 300)->nullable();
            $table->boolean('mandatory')->default(false);
            $table->boolean('enabled')->default(true);
            $table->boolean('wired')->default(false)->comment('Module nguồn đã phát sự kiện này');
            $table->standardColumns();
        });

        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('event_code', 20);
            $table->boolean('in_app')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'event_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('event_subscriptions');
    }
};
