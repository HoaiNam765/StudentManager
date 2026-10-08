<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Quản lý người dùng (FR-AUTH-008, 009; BR-AUTH-01, 07):
 *   - trạng thái tài khoản: active | locked (khóa hẳn, khác khóa tạm) | inactive (ngừng) | read_only (chỉ đọc);
 *   - hẹn ngày ngừng tài khoản (sinh viên thôi học, buộc thôi học, chuyển trường: ngừng sau thời gian cấu hình);
 *   - liên kết đúng một hồ sơ chính: sinh viên, giảng viên hoặc nhân viên. Bảng hồ sơ do STU, TCH tạo sau,
 *     nên `profile_id` để trống được; một hồ sơ chỉ gắn với một tài khoản.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('status', 20)->default('active')->index()->after('password');
            $table->string('status_reason', 500)->nullable()->after('status');
            $table->timestamp('status_changed_at')->nullable()->after('status_reason');
            $table->timestamp('deactivate_at')->nullable()->index()->after('status_changed_at')->comment('Hẹn ngừng tài khoản (BR-AUTH-07)');
            $table->string('profile_type', 20)->nullable()->after('deactivate_at')->comment('student | teacher | staff');
            $table->unsignedBigInteger('profile_id')->nullable()->after('profile_type');

            $table->unique(['profile_type', 'profile_id']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['profile_type', 'profile_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['deactivate_at']);
            $table->dropColumn(['status', 'status_reason', 'status_changed_at', 'deactivate_at', 'profile_type', 'profile_id']);
        });
    }
};
