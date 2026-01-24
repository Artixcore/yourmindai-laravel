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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('session_id')->nullable()->constrained('therapy_sessions')->onDelete('cascade');
            $table->enum('review_type', ['doctor', 'session']);
            $table->decimal('overall_rating', 3, 2);
            $table->text('comment')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->enum('status', ['pending', 'published', 'flagged'])->default('published');
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['doctor_id', 'status']);
            $table->index(['patient_id', 'review_type']);
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
