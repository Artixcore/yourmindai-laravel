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
        Schema::create('patient_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->nullable(); // For patients table
            $table->unsignedBigInteger('patient_profile_id')->nullable(); // For patient_profiles table
            $table->string('device_name');
            $table->enum('device_type', ['mobile', 'tablet', 'desktop'])->default('mobile');
            $table->string('device_identifier', 100)->unique(); // UUID
            $table->string('os_type')->nullable(); // iOS, Android, Windows, etc.
            $table->string('os_version')->nullable();
            $table->string('app_version')->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('registered_at')->nullable();
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('patient_profile_id')->references('id')->on('patient_profiles')->onDelete('cascade');
            $table->index('patient_id');
            $table->index('patient_profile_id');
            $table->index('device_identifier');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_devices');
    }
};
