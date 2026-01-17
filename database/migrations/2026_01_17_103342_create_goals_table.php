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
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->nullable();
            $table->boolean('visible_to_patient')->default(false);
            $table->boolean('visible_to_parent')->default(false);
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patient_profiles')->onDelete('cascade');
            $table->index('patient_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};
