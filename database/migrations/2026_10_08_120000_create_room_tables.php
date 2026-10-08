<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cơ sở, tòa nhà, loại phòng, phòng học và lịch bảo trì (FR-ROM-001, FR-ROM-002; BR-ROM-01, 02, 04).
 * Lịch sử dụng và tìm phòng trống làm ở P2; TTB và EXM đăng ký tham chiếu tới rooms khi có.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campuses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('address', 500)->nullable();
            $table->activeStatus();
            $table->searchText();
            $table->standardColumns();
        });

        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_id')->constrained()->restrictOnDelete();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->unsignedTinyInteger('floors')->nullable()->comment('Số tầng; trống = không giới hạn khi kiểm tra tầng của phòng');
            $table->activeStatus();
            $table->searchText();
            $table->standardColumns();
        });

        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('description', 500)->nullable();
            $table->activeStatus();
            $table->standardColumns();
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->restrictOnDelete();
            $table->foreignId('room_type_id')->constrained()->restrictOnDelete();
            $table->string('code', 30)->unique()->comment('Mã phòng duy nhất toàn trường (BR-ROM-01)');
            $table->string('name')->nullable();
            $table->smallInteger('floor')->default(1);
            $table->unsignedSmallInteger('capacity')->comment('Sức chứa học, số nguyên dương');
            $table->unsignedSmallInteger('exam_capacity')->default(0)->comment('Sức chứa thi, không lớn hơn sức chứa học; 0 = không dùng làm phòng thi');
            $table->json('equipment')->nullable()->comment('Danh sách thiết bị, ví dụ ["Máy chiếu", "Điều hòa"]');
            $table->string('note', 500)->nullable();
            $table->string('status', 20)->default('available')->index()->comment('available | maintenance | inactive');
            $table->searchText();
            $table->standardColumns();

            $table->index(['building_id', 'status']);
            $table->index(['room_type_id', 'capacity']);
        });

        Schema::create('room_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->string('reason', 500);
            $table->standardColumns();

            $table->index(['room_id', 'starts_on', 'ends_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_maintenances');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('room_types');
        Schema::dropIfExists('buildings');
        Schema::dropIfExists('campuses');
    }
};
