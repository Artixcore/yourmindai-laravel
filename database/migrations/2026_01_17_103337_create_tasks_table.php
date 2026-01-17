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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->integer('points')->default(0);
            $table->unsignedBigInteger('assigned_by_doctor_id')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->boolean('visible_to_patient')->default(true);
            $table->boolean('visible_to_parent')->default(true);
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patient_profiles')->onDelete('cascade');
            $table->foreign('assigned_by_doctor_id')->references('id')->on('users')->onDelete('set null');
            $table->index('patient_id');
            $table->index('assigned_by_doctor_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
