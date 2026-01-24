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
        Schema::create('exercise_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('homework_assignment_id')->nullable()->constrained('homework_assignments')->onDelete('set null');
            $table->date('exercise_date');
            $table->time('start_time')->nullable();
            $table->string('exercise_type')->comment('e.g., walking, yoga, gym, etc.');
            $table->integer('duration_minutes')->nullable();
            $table->integer('intensity')->nullable()->comment('1-10 scale');
            $table->integer('calories_burned')->nullable();
            $table->text('notes')->nullable();
            $table->json('vitals')->nullable()->comment('Heart rate, etc.');
            $table->timestamps();
            
            $table->index(['patient_id', 'exercise_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercise_logs');
    }
};
