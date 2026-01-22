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
        Schema::create('contingency_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->nullable(); // For patients table
            $table->unsignedBigInteger('patient_profile_id')->nullable(); // For patient_profiles table
            $table->unsignedBigInteger('created_by_doctor_id');
            $table->string('title');
            $table->json('trigger_conditions'); // Conditions that activate the plan
            $table->json('actions'); // Actions to take when triggered
            $table->json('emergency_contacts')->nullable(); // Emergency contact information
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('patient_profile_id')->references('id')->on('patient_profiles')->onDelete('cascade');
            $table->foreign('created_by_doctor_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('patient_id');
            $table->index('patient_profile_id');
            $table->index('status');
            $table->index('created_by_doctor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contingency_plans');
    }
};
