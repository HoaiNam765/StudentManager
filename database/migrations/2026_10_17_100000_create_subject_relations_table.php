<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Quan hệ giữa các học phần (FR-SUB-004; BR-SUB-03, 07).
 *
 * Một dòng đọc là: học phần `subject_id` có quan hệ `type` với học phần `related_subject_id`, ví dụ
 * (B, A, prerequisite) = "A là tiên quyết của B". Quan hệ tương đương có hiệu lực theo ngày; các quan hệ khác
 * không cần ngày (thay đổi lớn đi qua phiên bản học phần, FR-SUB-007).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subject_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->foreignId('related_subject_id')->constrained('subjects')->restrictOnDelete();
            $table->string('type', 20)->comment('prerequisite | prior | corequisite | equivalent');
            $table->date('effective_from')->nullable()->comment('Chỉ dùng cho tương đương');
            $table->date('effective_to')->nullable();
            $table->string('decision_no', 100)->nullable()->comment('Số quyết định của khoa (tương đương)');
            $table->string('note', 500)->nullable();
            $table->standardColumns();

            $table->index(['subject_id', 'type']);
            $table->index(['related_subject_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_relations');
    }
};
