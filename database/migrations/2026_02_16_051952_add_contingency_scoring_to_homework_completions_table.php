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
        Schema::table('homework_completions', function (Blueprint $table) {
            $table->string('scoring_choice', 50)->nullable()->after('completion_data')
                ->comment('self_action +10, others_help +5, not_working -10');
            $table->integer('score_value')->nullable()->after('scoring_choice');
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('score_value');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');

            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('homework_completions', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['scoring_choice', 'score_value', 'reviewed_by', 'reviewed_at']);
        });
    }
};
