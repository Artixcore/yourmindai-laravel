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
        Schema::create('patient_journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->integer('mood_score')->nullable(); // 1-10 scale
            $table->text('notes')->nullable();
            $table->date('entry_date');
            $table->json('tags')->nullable(); // e.g., ["anxious", "tired", "happy"]
            $table->timestamps();
            
            $table->index(['patient_id', 'entry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_journal_entries');
    }
};
