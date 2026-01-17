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
        Schema::create('invite_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type');
            $table->unsignedBigInteger('creator_id');
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->boolean('used')->default(false);
            $table->dateTime('used_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patient_profiles')->onDelete('set null');
            $table->index('code');
            $table->index('creator_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invite_codes');
    }
};
