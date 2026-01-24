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
        Schema::create('sleep_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('homework_assignment_id')->nullable()->constrained('homework_assignments')->onDelete('set null');
            $table->date('sleep_date');
            $table->time('bedtime')->nullable();
            $table->time('wake_time')->nullable();
            $table->decimal('hours_slept', 4, 2)->nullable();
            $table->integer('sleep_quality')->nullable()->comment('1-10 scale');
            $table->integer('times_woken')->default(0);
            $table->text('notes')->nullable();
            $table->json('factors')->nullable()->comment('Factors affecting sleep');
            $table->timestamps();
            
            $table->index(['patient_id', 'sleep_date']);
            $table->unique(['patient_id', 'sleep_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sleep_logs');
    }
};
