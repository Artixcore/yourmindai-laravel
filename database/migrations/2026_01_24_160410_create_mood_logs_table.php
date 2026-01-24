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
        Schema::create('mood_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('homework_assignment_id')->nullable()->constrained('homework_assignments')->onDelete('set null');
            $table->date('log_date');
            $table->time('log_time')->nullable();
            $table->integer('mood_rating')->comment('1-10 scale');
            $table->string('mood_emoji')->nullable()->comment('Emoji representation');
            $table->text('notes')->nullable();
            $table->json('triggers')->nullable()->comment('What triggered the mood');
            $table->json('activities')->nullable()->comment('Activities during this mood');
            $table->timestamps();
            
            $table->index(['patient_id', 'log_date']);
            $table->unique(['patient_id', 'log_date', 'log_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mood_logs');
    }
};
