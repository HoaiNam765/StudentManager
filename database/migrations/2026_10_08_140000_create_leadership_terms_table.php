<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lãnh đạo đơn vị theo nhiệm kỳ (FR-FAC-006; BR-FAC-04, 06): trưởng/phó khoa, trưởng/phó bộ môn, từ ngày – đến ngày.
 * Không ghi đè lịch sử (GC-06): kết thúc nhiệm kỳ bằng cách đặt ngày kết thúc; nhiệm kỳ chưa bắt đầu mới hủy được.
 *
 * Mỗi nhiệm kỳ sinh một dòng vai trò DEAN trong user_roles có cùng ngày hiệu lực (`user_role_id`),
 * nên tới ngày bắt đầu thì quyền và phạm vi FACULTY tự có hiệu lực, hết nhiệm kỳ thì tự hết.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leadership_terms', function (Blueprint $table) {
            $table->id();
            $table->string('unit_type', 20)->comment('faculty | department');
            $table->unsignedBigInteger('unit_id');
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('position', 20)->comment('dean | vice_dean | head | vice_head');
            $table->date('starts_on');
            $table->date('ends_on')->nullable()->comment('Trống = chưa xác định ngày kết thúc');
            $table->foreignId('user_role_id')->nullable()->constrained('user_roles')->nullOnDelete()->comment('Dòng vai trò DEAN sinh ra từ nhiệm kỳ');
            $table->string('note', 500)->nullable();
            $table->standardColumns();

            $table->index(['unit_type', 'unit_id', 'position', 'starts_on']);
            $table->index(['user_id', 'starts_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leadership_terms');
    }
};
