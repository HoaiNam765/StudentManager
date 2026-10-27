<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_periods', function (Blueprint $table) {
            $table->foreignId('training_framework_id')->nullable()->constrained()->restrictOnDelete();
            $table->timestamp('preview_at')->nullable();
        });
        Schema::table('course_sections', function (Blueprint $table) {
            $table->foreignId('registration_period_id')->nullable()->constrained()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('course_sections', fn (Blueprint $table) => $table->dropConstrainedForeignId('registration_period_id'));
        Schema::table('registration_periods', function (Blueprint $table) {
            $table->dropConstrainedForeignId('training_framework_id');
            $table->dropColumn('preview_at');
        });
    }
};
