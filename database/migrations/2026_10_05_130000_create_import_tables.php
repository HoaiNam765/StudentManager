<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Trung tâm import dùng chung (FR-SYS-007, BR-SYS-07).
 *
 * import_batches: mỗi lần import là một lô có mã duy nhất.
 * import_rows: từng dòng dữ liệu trong lô, kèm trạng thái kiểm tra.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_batches', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique()->comment('Mã lô, ví dụ: IMP-STU-20261005-0001 (BR-SYS-07)');
            $table->string('importer', 100)->index()->comment('Định danh Importer đã đăng ký, ví dụ: student, teacher, subject');
            $table->string('original_filename', 255)->comment('Tên file gốc người dùng tải lên');
            $table->string('disk', 30)->default('local')->comment('Tên disk lưu file');
            $table->string('path', 500)->comment('Đường dẫn file trên disk');
            $table->string('status', 20)->default('pending')->index()
                ->comment('pending | validating | validated | saving | saved | rolled_back | failed');
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('valid_rows')->default(0);
            $table->unsignedInteger('invalid_rows')->default(0);
            $table->unsignedInteger('saved_rows')->default(0);
            $table->string('save_mode', 20)->nullable()
                ->comment('all_valid (chỉ lưu dòng hợp lệ) | all (lưu toàn bộ, lỗi rollback)');
            $table->text('error_summary')->nullable()->comment('Tóm tắt lỗi toàn lô');
            $table->unsignedSmallInteger('progress')->default(0)->comment('Tiến trình 0–100 (%)');
            $table->string('job_id', 100)->nullable()->comment('ID queue job khi chạy nền');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->standardColumns(); // created_by, updated_by, timestamps, soft_deletes
        });

        Schema::create('import_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')->constrained('import_batches')->cascadeOnDelete();
            $table->unsignedInteger('row_number')->comment('Số thứ tự dòng trong file (bắt đầu từ 2 nếu có header)');
            $table->json('raw_data')->comment('Dữ liệu thô của dòng theo cột');
            $table->string('status', 20)->default('pending')->index()
                ->comment('pending | valid | invalid | saved | skipped');
            $table->json('errors')->nullable()->comment('Mảng [{column, message}] báo lỗi theo cột');
            $table->unsignedBigInteger('saved_model_id')->nullable()->comment('ID bản ghi đã lưu, dùng để hoàn tác');
            $table->string('saved_model_type', 100)->nullable()->comment('Model class khi đã lưu');
            $table->timestamps();

            $table->index(['import_batch_id', 'status']);
            $table->index(['import_batch_id', 'row_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_rows');
        Schema::dropIfExists('import_batches');
    }
};
