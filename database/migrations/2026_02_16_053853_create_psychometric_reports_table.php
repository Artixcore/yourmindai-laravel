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
        Schema::create('psychometric_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('psychometric_assessments')->onDelete('cascade');
            $table->unsignedBigInteger('patient_profile_id')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->text('summary');
            $table->string('pdf_path')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->foreign('patient_profile_id')->references('id')->on('patient_profiles')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->index(['assessment_id', 'patient_profile_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psychometric_reports');
    }
};
