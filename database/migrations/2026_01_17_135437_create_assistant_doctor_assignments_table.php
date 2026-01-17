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
        Schema::create('assistant_doctor_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assistant_id');
            $table->unsignedBigInteger('doctor_id');
            $table->timestamps();

            $table->foreign('assistant_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
            
            // Unique constraint: one assistant can only be assigned to a doctor once
            $table->unique(['assistant_id', 'doctor_id']);
            
            // Indexes for performance
            $table->index('assistant_id');
            $table->index('doctor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assistant_doctor_assignments');
    }
};
