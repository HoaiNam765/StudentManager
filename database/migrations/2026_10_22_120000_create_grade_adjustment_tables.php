<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->restrictOnDelete();
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('faculty_approved_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('status', 24)->default('pending_faculty');
            $table->text('reason');
            $table->string('evidence_path')->nullable();
            $table->json('proposed_scores');
            $table->text('rejection_reason')->nullable();
            $table->standardColumns();
            $table->index(['enrollment_id', 'status']);
        });
        Schema::create('grade_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->restrictOnDelete();
            $table->foreignId('grade_change_request_id')->nullable()->constrained()->restrictOnDelete();
            $table->unsignedInteger('version');
            $table->json('snapshot');
            $table->string('reason', 2000);
            $table->standardColumns();
            $table->unique(['enrollment_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_versions');
        Schema::dropIfExists('grade_change_requests');
    }
};
