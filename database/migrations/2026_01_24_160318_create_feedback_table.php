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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->morphs('feedbackable'); // polymorphic relationship
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->enum('source', ['parent', 'self', 'others', 'therapist'])->default('self');
            $table->foreignId('source_user_id')->nullable()->constrained('users')->onDelete('set null'); // who gave the feedback
            $table->text('feedback_text')->nullable();
            $table->integer('rating')->nullable()->comment('1-5 or 1-10 scale');
            $table->json('custom_data')->nullable()->comment('For flexible feedback data');
            $table->timestamp('feedback_date')->useCurrent();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['feedbackable_type', 'feedbackable_id']);
            $table->index(['patient_id', 'source']);
            $table->index('feedback_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
