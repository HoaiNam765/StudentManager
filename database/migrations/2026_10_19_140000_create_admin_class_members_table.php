<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Thành viên lớp hành chính có ngày hiệu lực (BR-CLS-08, BR-STU-04): mỗi sinh viên đúng một lớp hiệu lực tại một thời điểm,
 * chuyển lớp thì kết thúc dòng cũ và mở dòng mới. #102 gán lớp khi xác nhận nhập học; gán / gỡ hàng loạt, cố vấn ở #99.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_class_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_class_id')->constrained()->restrictOnDelete();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->string('reason', 500)->nullable();
            $table->standardColumns();

            $table->index(['admin_class_id', 'effective_to']);
            $table->index(['student_id', 'effective_from']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_class_members');
    }
};
