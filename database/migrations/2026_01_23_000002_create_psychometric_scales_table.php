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
        Schema::create('psychometric_scales', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "PHQ-9", "GAD-7"
            $table->text('description')->nullable();
            $table->string('category')->nullable(); // e.g., "Depression", "Anxiety"
            $table->json('questions'); // Array of question objects
            $table->json('scoring_rules'); // How to calculate scores
            $table->json('interpretation_rules'); // How to interpret scores
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by_doctor_id')->nullable();
            $table->timestamps();

            $table->foreign('created_by_doctor_id')->references('id')->on('users')->onDelete('set null');
            $table->index('is_active');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psychometric_scales');
    }
};
