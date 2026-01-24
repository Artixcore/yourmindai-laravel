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
        Schema::create('homework_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade')->comment('Doctor who assigned');
            $table->string('session_id')->nullable();
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('set null');
            $table->enum('homework_type', [
                'psychotherapy',
                'lifestyle_modification',
                'sleep_tracking',
                'mood_tracking',
                'personal_journal',
                'risk_tracking',
                'contingency',
                'exercise',
                'parent_role',
                'others_role',
                'self_help_tools'
            ])->comment('Type of therapy technique');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->json('goals')->nullable()->comment('Specific goals for this assignment');
            $table->enum('frequency', ['daily', 'weekly', 'as_needed'])->default('daily');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['assigned', 'in_progress', 'completed', 'cancelled'])->default('assigned');
            $table->boolean('requires_parent_feedback')->default(false);
            $table->boolean('requires_others_feedback')->default(false);
            $table->json('custom_fields')->nullable();
            $table->timestamps();
            
            $table->index(['patient_id', 'homework_type', 'status']);
            $table->index(['assigned_by', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homework_assignments');
    }
};
