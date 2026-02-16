<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('homework_assignments', function (Blueprint $table) {
            $table->string('frequency_type', 50)->nullable()->after('frequency')
                ->comment('times_per_day, days_per_week, schedule_rules, as_before');
            $table->json('frequency_value')->nullable()->after('frequency_type');
            $table->timestamp('reminder_at')->nullable()->after('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('homework_assignments', function (Blueprint $table) {
            $table->dropColumn(['frequency_type', 'frequency_value', 'reminder_at']);
        });
    }
};
