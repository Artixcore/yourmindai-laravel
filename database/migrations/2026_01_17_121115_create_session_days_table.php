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
        Schema::create('session_days', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('session_id');
            $table->date('day_date');
            $table->longText('symptoms')->nullable();
            $table->longText('alerts')->nullable();
            $table->longText('tasks')->nullable();
            $table->timestamps();

            // Foreign key constraint with cascade delete
            $table->foreign('session_id')->references('id')->on('therapy_sessions')->onDelete('cascade');

            // Unique constraint: one entry per date per session
            $table->unique(['session_id', 'day_date']);

            // Index for performance
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_days');
    }
};
