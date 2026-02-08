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
        Schema::create('lifestyle_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->string('type', 50);
            $table->string('label', 255)->nullable();
            $table->text('value')->nullable();
            $table->date('logged_date');
            $table->timestamps();
            $table->index(['patient_id', 'logged_date', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lifestyle_logs');
    }
};
