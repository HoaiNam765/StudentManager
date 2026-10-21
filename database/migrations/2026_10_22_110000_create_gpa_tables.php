<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('term_gpas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('term_id')->constrained()->restrictOnDelete();
            $table->decimal('gpa', 4, 2)->nullable();
            $table->unsignedInteger('credits_attempted')->default(0);
            $table->unsignedInteger('credits_earned')->default(0);
            $table->string('classification', 30)->nullable();
            $table->standardColumns();
            $table->unique(['student_id', 'term_id']);
        });
        Schema::create('cumulative_gpas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained()->restrictOnDelete();
            $table->decimal('gpa', 4, 2)->nullable();
            $table->unsignedInteger('credits_attempted')->default(0);
            $table->unsignedInteger('credits_earned')->default(0);
            $table->string('classification', 30)->nullable();
            $table->standardColumns();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cumulative_gpas');
        Schema::dropIfExists('term_gpas');
    }
};
