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
        Schema::create('parent_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->string('permission_type')->comment('e.g., view_psychotherapy, view_mood_tracking, etc.');
            $table->boolean('granted')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Unique constraint: parent + patient + permission_type combination must be unique
            $table->unique(['parent_id', 'patient_id', 'permission_type']);
            
            // Indexes
            $table->index(['parent_id', 'patient_id']);
            $table->index('permission_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_permissions');
    }
};
