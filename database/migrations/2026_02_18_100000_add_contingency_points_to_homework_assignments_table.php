<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add doctor-set contingency scoring points (self_action, others_help, not_working).
     * When null, fall back to defaults: +10, +5, -10.
     */
    public function up(): void
    {
        Schema::table('homework_assignments', function (Blueprint $table) {
            $table->integer('contingency_self_action_points')->nullable()->after('custom_fields');
            $table->integer('contingency_others_help_points')->nullable()->after('contingency_self_action_points');
            $table->integer('contingency_not_working_points')->nullable()->after('contingency_others_help_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('homework_assignments', function (Blueprint $table) {
            $table->dropColumn([
                'contingency_self_action_points',
                'contingency_others_help_points',
                'contingency_not_working_points',
            ]);
        });
    }
};
