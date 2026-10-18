<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lớp học phần và phân công giảng viên (FR-CLS-009..012; BR-CLS-02, 03, 04).
 *
 * - `course_sections`: mã tự sinh duy nhất (mã học phần + học kỳ + số thứ tự, FR-CLS-010); học phần, học kỳ, loại,
 *   hình thức, sĩ số tối thiểu – tối đa, ngôn ngữ, phòng (TTB xếp sau), trạng thái theo vòng đời FR-CLS-012.
 *   Lớp học phần không gắn với lớp hành chính: sinh viên nhiều lớp, nhiều ngành học chung (lớp ghép).
 * - `section_instructors`: giảng viên chính / trợ giảng; mỗi giảng viên một lần trong một lớp.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_sections', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique()->comment('Mã lớp học phần (FR-CLS-010)');
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->foreignId('term_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('sequence')->comment('Số thứ tự lớp của học phần trong học kỳ');
            $table->string('section_type', 20)->default('theory')->comment('theory | practice | mixed');
            $table->string('delivery_mode', 20)->default('in_person')->comment('in_person | online | hybrid');
            $table->string('language', 10)->default('vi');
            $table->unsignedSmallInteger('min_size');
            $table->unsignedSmallInteger('max_size');
            $table->foreignId('room_id')->nullable()->constrained()->restrictOnDelete()->comment('Phòng chính (TTB xếp)');
            $table->string('status', 30)->default('planned')
                ->comment('planned | registration_open | registration_closed | in_progress | finished | locked | cancelled');
            $table->string('cancel_reason', 500)->nullable();
            $table->string('note', 500)->nullable();
            $table->searchText();
            $table->standardColumns();

            $table->unique(['subject_id', 'term_id', 'sequence']);
            $table->index(['term_id', 'status']);
        });

        Schema::create('section_instructors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')->constrained()->restrictOnDelete();
            $table->foreignId('lecturer_id')->constrained()->restrictOnDelete();
            $table->string('role', 20)->default('main')->comment('main | assistant');
            $table->standardColumns();

            $table->unique(['course_section_id', 'lecturer_id']);
            $table->index(['lecturer_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_instructors');
        Schema::dropIfExists('course_sections');
    }
};
