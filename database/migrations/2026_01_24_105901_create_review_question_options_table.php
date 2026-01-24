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
        Schema::create('review_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('review_questions')->onDelete('cascade');
            $table->string('option_text');
            $table->string('option_value');
            $table->integer('order')->default(0);
            $table->timestamps();
            
            // Index for performance
            $table->index(['question_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_question_options');
    }
};
