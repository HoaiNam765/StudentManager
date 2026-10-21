<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section_grade_workflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')->unique()->constrained()->restrictOnDelete();
            $table->string('status', 24)->default('draft');
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('return_reason')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->standardColumns();
        });

        Schema::create('course_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->unique()->constrained()->restrictOnDelete();
            $table->decimal('score_10', 4, 1)->nullable();
            $table->string('letter', 3)->nullable();
            $table->decimal('score_4', 3, 2)->nullable();
            $table->boolean('passed')->nullable();
            $table->string('special_status', 24)->nullable();
            $table->standardColumns();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_results');
        Schema::dropIfExists('section_grade_workflows');
    }
};
