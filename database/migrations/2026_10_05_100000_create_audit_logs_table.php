<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Nhật ký kiểm toán (FR-SYS-004, BR-SYS-01, GC-03): ai, làm gì, trên đối tượng nào,
 * khi nào, từ đâu, giá trị trước và sau, lý do. Bảng chỉ được ghi thêm.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event', 50)->index();
            $table->nullableMorphs('auditable');
            // Không đặt khóa ngoại: nhật ký phải giữ nguyên kể cả khi tài khoản thay đổi; lưu kèm tên tại thời điểm ghi
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('user_name')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('reason', 500)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('url', 500)->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });

        // Chặn sửa và xóa ở tầng CSDL, kể cả khi ai đó dùng query builder hoặc SQL trực tiếp (BR-SYS-01)
        if (DB::getDriverName() === 'mysql') {
            DB::unprepared("
                CREATE TRIGGER audit_logs_no_update BEFORE UPDATE ON audit_logs FOR EACH ROW
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'audit_logs is append-only: update is not allowed (BR-SYS-01)'
            ");
            DB::unprepared("
                CREATE TRIGGER audit_logs_no_delete BEFORE DELETE ON audit_logs FOR EACH ROW
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'audit_logs is append-only: delete is not allowed (BR-SYS-01)'
            ");
        }
    }

    public function down(): void
    {
        // Xóa bảng thì trigger cũng bị xóa theo
        Schema::dropIfExists('audit_logs');
    }
};
