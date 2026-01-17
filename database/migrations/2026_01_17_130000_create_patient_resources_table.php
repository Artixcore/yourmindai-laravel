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
        Schema::create('patient_resources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('session_id')->nullable();
            $table->unsignedBigInteger('session_day_id')->nullable();
            $table->enum('type', ['pdf', 'youtube']);
            $table->string('title');
            $table->string('file_path')->nullable();
            $table->string('youtube_url')->nullable();
            $table->timestamps();

            // Foreign key constraints with cascade delete
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
            $table->foreign('session_day_id')->references('id')->on('session_days')->onDelete('cascade');

            // Indexes for performance
            $table->index('doctor_id');
            $table->index('patient_id');
            $table->index('session_id');
            $table->index('session_day_id');
            $table->index(['patient_id', 'session_id', 'session_day_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_resources');
    }
};
