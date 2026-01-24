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
        Schema::create('general_assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('general_assessments')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->string('question_text');
            $table->enum('question_type', ['text', 'multiple_choice', 'scale', 'yes_no'])->default('text');
            $table->json('options')->nullable()->comment('For multiple choice questions');
            $table->boolean('is_required')->default(false);
            $table->timestamps();
            
            $table->index(['assessment_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_assessment_questions');
    }
};
