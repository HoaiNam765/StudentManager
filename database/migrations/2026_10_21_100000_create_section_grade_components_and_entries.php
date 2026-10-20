<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section_grade_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')->constrained()->restrictOnDelete();
            $table->foreignId('grade_component_template_id')->nullable()->constrained()->restrictOnDelete();
            $table->unsignedInteger('version')->default(1);
            $table->string('name', 100);
            $table->string('kind', 20);
            $table->decimal('weight', 5, 2);
            $table->decimal('min_score', 4, 2)->nullable();
            $table->boolean('is_required')->default(true);
            $table->boolean('is_remote')->default(false);
            $table->unsignedTinyInteger('sort_order');
            $table->standardColumns();

            $table->unique(['course_section_id', 'version', 'sort_order'], 'section_grade_components_version_order_unique');
            $table->index(['course_section_id', 'deleted_at']);
        });

        Schema::create('grade_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->restrictOnDelete();
            $table->foreignId('section_grade_component_id')->constrained()->restrictOnDelete();
            $table->decimal('score', 4, 2)->nullable();
            $table->string('status', 20)->default('draft')->comment('draft | missing');
            $table->standardColumns();

            $table->unique(['enrollment_id', 'section_grade_component_id'], 'grade_entries_enrollment_component_unique');
            $table->index(['section_grade_component_id', 'status']);
        });

        DB::statement('ALTER TABLE grade_entries ADD CONSTRAINT grade_entries_score_range CHECK (score IS NULL OR (score >= 0 AND score <= 10))');
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_entries');
        Schema::dropIfExists('section_grade_components');
    }
};
