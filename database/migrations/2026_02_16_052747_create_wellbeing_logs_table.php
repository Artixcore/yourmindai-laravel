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
        Schema::create('wellbeing_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_profile_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->date('log_date');
            $table->integer('screentime_minutes')->nullable()->comment('Daily screen time in minutes');
            $table->json('details')->nullable()->comment('Additional screentime breakdown or notes');
            $table->json('lifestyle_errors')->nullable()->comment('Logged issues/flags');
            $table->timestamps();

            $table->index(['patient_profile_id', 'log_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wellbeing_logs');
    }
};
