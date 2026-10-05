<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->id();

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            $table->string('name', 100);

            $table->string('type', 20);

            $table->date('start_date');
            $table->date('end_date');

            $table->unsignedSmallInteger('weeks');

            $table->string('status', 30)
                ->default('planned');

            $table->standardColumns();

            $table->index([
                'academic_year_id',
                'type',
                'start_date',
                'end_date',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terms');
    }
};
