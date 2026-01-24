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
        Schema::create('routines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade')->comment('Doctor who created');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('frequency', ['daily', 'weekdays', 'weekends', 'custom'])->default('daily');
            $table->time('start_time')->nullable()->comment('Recommended start time');
            $table->boolean('is_active')->default(true);
            $table->json('custom_schedule')->nullable()->comment('For custom frequency');
            $table->timestamps();
            
            $table->index(['patient_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routines');
    }
};
