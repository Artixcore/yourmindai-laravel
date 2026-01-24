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
        Schema::create('homework_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('homework_assignment_id')->constrained('homework_assignments')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->date('completion_date');
            $table->time('completion_time')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->integer('completion_percentage')->default(0)->comment('0-100');
            $table->text('patient_notes')->nullable();
            $table->json('completion_data')->nullable()->comment('Flexible data based on homework type');
            $table->timestamps();
            
            $table->index(['homework_assignment_id', 'completion_date']);
            $table->index(['patient_id', 'completion_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homework_completions');
    }
};
