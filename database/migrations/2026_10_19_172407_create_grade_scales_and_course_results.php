
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_scales', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('cohort');
            $table->decimal('min_score', 4, 2);
            $table->string('letter', 3);
            $table->decimal('point4', 3, 1);
            $table->standardColumns();

            $table->unique(['cohort', 'min_score']);
        });

        Schema::create('course_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')
                ->unique()
                ->constrained()
                ->restrictOnDelete();
            $table->decimal('score10', 4, 2)->nullable();
            $table->string('letter', 3)->nullable();
            $table->decimal('point4', 3, 1)->nullable();
            $table->boolean('is_passed')->nullable();
            $table->string('status', 30)->default('incomplete');
            $table->standardColumns();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_results');
        Schema::dropIfExists('grade_scales');
    }
};
