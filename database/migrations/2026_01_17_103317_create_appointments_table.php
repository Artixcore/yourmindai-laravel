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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('patient_id');
            $table->dateTime('date');
            $table->string('time_slot')->nullable();
            $table->string('status')->nullable();
            $table->string('appointment_type')->nullable();
            $table->string('setting_place')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('reminder_enabled')->default(false);
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patient_profiles')->onDelete('cascade');
            $table->index('doctor_id');
            $table->index('patient_id');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
