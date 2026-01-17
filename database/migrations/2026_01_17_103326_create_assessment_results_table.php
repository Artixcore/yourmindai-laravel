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
        Schema::create('assessment_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->integer('score')->nullable();
            $table->json('sub_scores')->nullable();
            $table->text('interpretation')->nullable();
            $table->json('responses')->nullable();
            $table->string('substance_type')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            $table->index('assessment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_results');
    }
};
