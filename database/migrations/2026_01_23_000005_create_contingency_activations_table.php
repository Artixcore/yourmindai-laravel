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
        Schema::create('contingency_activations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contingency_plan_id');
            $table->unsignedBigInteger('patient_id')->nullable(); // For patients table
            $table->unsignedBigInteger('patient_profile_id')->nullable(); // For patient_profiles table
            $table->enum('triggered_by', ['self', 'doctor', 'parent', 'other'])->default('self');
            $table->text('trigger_reason')->nullable();
            $table->json('actions_taken')->nullable(); // Actions that were executed
            $table->text('outcome')->nullable();
            $table->timestamp('activated_at');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->foreign('contingency_plan_id')->references('id')->on('contingency_plans')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('patient_profile_id')->references('id')->on('patient_profiles')->onDelete('cascade');
            $table->index('contingency_plan_id');
            $table->index('patient_id');
            $table->index('patient_profile_id');
            $table->index('activated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contingency_activations');
    }
};
