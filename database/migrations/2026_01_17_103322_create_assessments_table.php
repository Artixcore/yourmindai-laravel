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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('assigned_by_doctor_id');
            $table->string('assessment_type')->nullable();
            $table->string('status')->nullable();
            $table->dateTime('assigned_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patient_profiles')->onDelete('cascade');
            $table->foreign('assigned_by_doctor_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('patient_id');
            $table->index('assigned_by_doctor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
