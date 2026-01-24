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
        Schema::create('routine_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('routine_id')->constrained('routines')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('time_of_day', ['morning', 'afternoon', 'evening', 'night', 'anytime'])->default('anytime');
            $table->time('scheduled_time')->nullable();
            $table->integer('estimated_minutes')->nullable();
            $table->boolean('is_required')->default(true);
            $table->timestamps();
            
            $table->index(['routine_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routine_items');
    }
};
