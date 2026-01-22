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
        Schema::create('psychometric_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->nullable(); // For patients table
            $table->unsignedBigInteger('patient_profile_id')->nullable(); // For patient_profiles table
            $table->unsignedBigInteger('scale_id');
            $table->unsignedBigInteger('assigned_by_doctor_id');
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->json('responses')->nullable(); // Patient responses
            $table->integer('total_score')->nullable();
            $table->json('sub_scores')->nullable(); // Sub-scale scores if applicable
            $table->text('interpretation')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('patient_profile_id')->references('id')->on('patient_profiles')->onDelete('cascade');
            $table->foreign('scale_id')->references('id')->on('psychometric_scales')->onDelete('cascade');
            $table->foreign('assigned_by_doctor_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('patient_id');
            $table->index('patient_profile_id');
            $table->index('scale_id');
            $table->index('status');
            $table->index('assigned_by_doctor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psychometric_assessments');
    }
};
