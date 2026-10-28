<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Đợt đăng ký theo chương trình khung cho cả học kỳ: một đợt gồm khung của nhiều khoa – khóa
 * (`registration_period_frameworks`), thay cho cột một khung `registration_periods.training_framework_id`.
 * `training_frameworks.planned_term`: kỳ CTĐT Phòng Đào tạo chọn cho khung (trống = học kỳ thứ n của khóa).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_period_frameworks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('training_framework_id')->constrained()->restrictOnDelete();
            $table->timestamps();

            $table->unique(['registration_period_id', 'training_framework_id'], 'period_framework_unique');
        });

        Schema::table('registration_periods', function (Blueprint $table) {
            $table->boolean('uses_frameworks')->default(false)->after('round');
        });

        Schema::table('training_frameworks', function (Blueprint $table) {
            $table->unsignedTinyInteger('planned_term')->nullable()->after('term_id')->comment('Kỳ CTĐT đã chọn; trống = học kỳ thứ n của khóa');
        });

        // Đợt một khung cũ → đợt theo khung có đúng khung đó
        $now = now();
        foreach (DB::table('registration_periods')->whereNotNull('training_framework_id')->get(['id', 'training_framework_id']) as $row) {
            DB::table('registration_period_frameworks')->insert([
                'registration_period_id' => $row->id, 'training_framework_id' => $row->training_framework_id,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            DB::table('registration_periods')->where('id', $row->id)->update(['uses_frameworks' => true]);
        }

        Schema::table('registration_periods', fn (Blueprint $table) => $table->dropConstrainedForeignId('training_framework_id'));
    }

    public function down(): void
    {
        Schema::table('registration_periods', function (Blueprint $table) {
            $table->foreignId('training_framework_id')->nullable()->constrained()->restrictOnDelete();
        });

        foreach (DB::table('registration_period_frameworks')->orderBy('id')->get() as $row) {
            DB::table('registration_periods')->where('id', $row->registration_period_id)->whereNull('training_framework_id')
                ->update(['training_framework_id' => $row->training_framework_id]);
        }

        Schema::table('training_frameworks', fn (Blueprint $table) => $table->dropColumn('planned_term'));
        Schema::table('registration_periods', fn (Blueprint $table) => $table->dropColumn('uses_frameworks'));
        Schema::dropIfExists('registration_period_frameworks');
    }
};
