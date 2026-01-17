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
        Schema::create('ai_reports', function (Blueprint $table) {
            $table->id();
            $table->enum('scope', ['patient', 'session', 'doctor', 'clinic']);
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('session_id')->nullable();
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->unsignedBigInteger('requested_by');
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->string('input_snapshot_hash')->nullable();
            $table->string('model')->default('gpt-4o-mini');
            $table->enum('status', ['queued', 'running', 'completed', 'failed'])->default('queued');
            $table->longText('result_summary')->nullable();
            $table->longText('result_json')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('therapy_sessions')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('cascade');

            // Indexes for performance
            $table->index('scope');
            $table->index('status');
            $table->index('requested_by');
            $table->index('patient_id');
            $table->index('doctor_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_reports');
    }
};
