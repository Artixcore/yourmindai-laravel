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
        Schema::create('sleep_hygiene_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_profile_id')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->foreignId('sleep_hygiene_item_id')->constrained('sleep_hygiene_items')->onDelete('cascade');
            $table->date('log_date');
            $table->boolean('is_completed')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('patient_profile_id')->references('id')->on('patient_profiles')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->index(['patient_profile_id', 'log_date']);
            $table->index(['patient_id', 'log_date']);
            $table->index(['sleep_hygiene_item_id', 'log_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sleep_hygiene_logs');
    }
};
