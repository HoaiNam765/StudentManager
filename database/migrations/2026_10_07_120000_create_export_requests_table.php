<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('export_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('module', 10);
            $table->string('format', 10);
            $table->string('filename', 150);
            $table->string('status', 20)->index();
            $table->unsignedBigInteger('row_count');
            $table->json('columns');
            $table->json('export_scopes');
            $table->json('view_scopes');
            $table->string('path')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('export_requests');
    }
};
