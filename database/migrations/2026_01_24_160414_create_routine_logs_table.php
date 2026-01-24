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
        Schema::create('routine_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('routine_item_id')->constrained('routine_items')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->date('log_date');
            $table->time('completed_at')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->boolean('is_skipped')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['patient_id', 'log_date']);
            $table->index(['routine_item_id', 'log_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routine_logs');
    }
};
