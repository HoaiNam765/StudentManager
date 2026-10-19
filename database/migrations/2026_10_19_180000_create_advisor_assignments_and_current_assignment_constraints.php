<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Cố vấn học tập và giới hạn một phân công đang mở trên mỗi lớp/sinh viên (FR-CLS-003, 004; BR-CLS-07, 08).
 * Các cột sinh nullable cho phép nhiều dòng lịch sử nhưng unique cho duy nhất dòng chưa kết thúc.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_class_members', function (Blueprint $table) {
            $table->string('end_reason', 500)->nullable()->after('reason');
            $table->unsignedBigInteger('current_student_id')
                ->nullable()
                ->storedAs('CASE WHEN effective_to IS NULL THEN student_id ELSE NULL END');
            $table->unique('current_student_id', 'admin_class_members_one_current_student');
            $table->index(['student_id', 'effective_from', 'effective_to'], 'admin_class_members_period_lookup');
        });

        Schema::create('advisor_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_class_id')->constrained()->restrictOnDelete();
            $table->foreignId('lecturer_id')->constrained()->restrictOnDelete();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->string('end_reason', 500)->nullable();
            $table->unsignedBigInteger('current_admin_class_id')
                ->nullable()
                ->storedAs('CASE WHEN effective_to IS NULL THEN admin_class_id ELSE NULL END');
            $table->string('reason', 500)->nullable();
            $table->standardColumns();

            $table->unique('current_admin_class_id', 'advisor_assignments_one_current_per_class');
            $table->index(['lecturer_id', 'effective_from', 'effective_to'], 'advisor_assignments_lecturer_period_lookup');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE advisor_assignments ADD CONSTRAINT advisor_assignments_effective_period_check CHECK (effective_to IS NULL OR effective_to >= effective_from)');
            DB::statement('ALTER TABLE admin_class_members ADD CONSTRAINT admin_class_members_effective_period_check CHECK (effective_to IS NULL OR effective_to >= effective_from)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('advisor_assignments');

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE admin_class_members DROP CHECK admin_class_members_effective_period_check');
        }

        Schema::table('admin_class_members', function (Blueprint $table) {
            $table->dropUnique('admin_class_members_one_current_student');
            $table->dropIndex('admin_class_members_period_lookup');
            $table->dropColumn(['current_student_id', 'end_reason']);
        });
    }
};
