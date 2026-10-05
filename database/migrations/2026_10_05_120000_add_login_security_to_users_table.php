<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Đăng nhập an toàn (FR-AUTH-001..006, BR-AUTH-06, BR-AUTH-10): tên đăng nhập, buộc đổi mật khẩu,
 * mật khẩu tạm có hạn, đếm số lần sai và khóa tạm, lịch sử mật khẩu.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // MSSV với sinh viên, mã cán bộ với nhân sự; đăng nhập được bằng tên này hoặc email
            $table->string('username', 50)->nullable()->unique()->after('id');
            $table->boolean('must_change_password')->default(false)->after('password');
            $table->timestamp('temporary_password_expires_at')->nullable()->after('must_change_password');
            $table->timestamp('password_changed_at')->nullable()->after('temporary_password_expires_at');
            $table->unsignedSmallInteger('failed_login_count')->default(0)->after('password_changed_at');
            $table->timestamp('failed_login_started_at')->nullable()->after('failed_login_count');
            $table->timestamp('locked_until')->nullable()->after('failed_login_started_at');
            $table->timestamp('last_login_at')->nullable()->after('locked_until');
        });

        Schema::create('password_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('password');
            $table->timestamp('created_at')->useCurrent();
            $table->index(['user_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_histories');

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn([
                'username', 'must_change_password', 'temporary_password_expires_at', 'password_changed_at',
                'failed_login_count', 'failed_login_started_at', 'locked_until', 'last_login_at',
            ]);
        });
    }
};
