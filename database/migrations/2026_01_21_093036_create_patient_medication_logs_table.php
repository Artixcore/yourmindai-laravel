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
        Schema::create('patient_medication_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medication_id')->constrained('patient_medications')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->date('taken_date');
            $table->time('taken_time')->nullable();
            $table->boolean('taken')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['patient_id', 'taken_date']);
            $table->index(['medication_id', 'taken_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_medication_logs');
    }
};
