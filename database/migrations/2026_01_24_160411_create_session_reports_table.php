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
        Schema::create('session_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('sessions')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade')->comment('Doctor who created');
            $table->string('title');
            $table->text('summary')->nullable();
            $table->text('assessments_summary')->nullable()->comment('Summary of assessments done');
            $table->text('techniques_assigned')->nullable()->comment('Summary of homework assigned');
            $table->text('progress_notes')->nullable();
            $table->text('next_steps')->nullable();
            $table->json('shared_with')->nullable()->comment('Array of user IDs shared with');
            $table->boolean('shared_with_patient')->default(true);
            $table->boolean('shared_with_parents')->default(true);
            $table->boolean('shared_with_others')->default(false);
            $table->enum('status', ['draft', 'finalized', 'shared'])->default('draft');
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();
            
            $table->index(['session_id', 'patient_id']);
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_reports');
    }
};
