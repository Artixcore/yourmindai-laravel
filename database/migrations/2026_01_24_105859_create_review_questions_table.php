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
        Schema::create('review_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question_text');
            $table->enum('question_type', ['star_rating', 'yes_no', 'multiple_choice']);
            $table->enum('applies_to', ['doctor', 'session', 'both']);
            $table->boolean('is_required')->default(true);
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('condition_field')->nullable();
            $table->string('condition_value')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['applies_to', 'is_active', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_questions');
    }
};
