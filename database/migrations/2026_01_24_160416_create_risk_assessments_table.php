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
        Schema::create('risk_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('homework_assignment_id')->nullable()->constrained('homework_assignments')->onDelete('set null');
            $table->foreignId('assessed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('assessment_date');
            $table->enum('risk_level', ['none', 'low', 'moderate', 'high', 'critical'])->default('none');
            $table->json('risk_factors')->nullable()->comment('Array of identified risk factors');
            $table->text('assessment_notes')->nullable();
            $table->text('intervention_plan')->nullable();
            $table->boolean('alert_sent')->default(false);
            $table->timestamp('alert_sent_at')->nullable();
            $table->timestamps();
            
            $table->index(['patient_id', 'assessment_date']);
            $table->index(['patient_id', 'risk_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_assessments');
    }
};
