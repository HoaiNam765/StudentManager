<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lịch sử đăng nhập (FR-AUTH-016): thành công hoặc thất bại, thời điểm, IP, thiết bị; đánh dấu đăng nhập bất thường
 * (EV-AUTH-02). Chỉ ghi thêm. `session_id` nối với bảng `sessions` để trang "Phiên đăng nhập" biết phiên bắt đầu lúc nào.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_histories', function (Blueprint $table) {
            $table->id();
            // Không có tài khoản (gõ sai tên đăng nhập) thì user_id rỗng, chỉ lưu chuỗi đã nhập
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('login', 100)->nullable();
            $table->boolean('successful');
            $table->string('failure_reason', 255)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('device', 100)->nullable();
            $table->string('device_type', 20)->nullable();
            $table->boolean('unusual')->default(false);
            $table->string('session_id')->nullable()->index();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index(['successful', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_histories');
    }
};
