<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phân quyền RBAC (FR-AUTH-010..013, BR-AUTH-04): vai trò, danh mục quyền theo module – hành động,
 * quyền của vai trò kèm phạm vi dữ liệu, và vai trò của người dùng theo thời gian hiệu lực.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('description', 500)->nullable();
            $table->boolean('is_system')->default(false);
            $table->activeStatus();
            $table->searchText();
            $table->standardColumns();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('module', 10);
            $table->string('action', 20);
            $table->string('name');
            $table->timestamps();
            $table->unique(['module', 'action']);
        });

        // Phạm vi dữ liệu đặt ở đây để cùng một quyền có phạm vi khác nhau theo từng vai trò
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->string('scope', 10);
            $table->timestamps();
            $table->unique(['role_id', 'permission_id']);
        });

        // Gỡ vai trò bằng cách đặt valid_to, không xóa dòng: giữ lịch sử (GC-06)
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['user_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
