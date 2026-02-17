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
        Schema::create('device_actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_profile_id')->nullable();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('device_id')->nullable();
            $table->string('action_type');
            $table->text('action_note')->nullable();
            $table->timestamps();

            $table->foreign('patient_profile_id')->references('id')->on('patient_profiles')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('device_id')->references('id')->on('patient_devices')->onDelete('set null');
            $table->index(['patient_profile_id', 'created_at']);
            $table->index(['patient_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_actions');
    }
};
