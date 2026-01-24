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
        Schema::create('review_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('reviews')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('review_questions')->onDelete('cascade');
            $table->text('answer_value');
            $table->text('answer_text')->nullable();
            $table->timestamps();
            
            // Composite index for efficient querying
            $table->index(['review_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_answers');
    }
};
