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
        Schema::create('practice_progressions', function (Blueprint $table) {
            $table->id();
            $table->morphs('progressionable', 'progressionable_idx'); // polymorphic relationship with custom index name
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->date('progress_date');
            $table->integer('progress_percentage')->default(0)->comment('0-100');
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'skipped'])->default('not_started');
            $table->text('notes')->nullable();
            $table->enum('monitored_by', ['parent', 'self', 'others', 'therapist'])->default('self');
            $table->foreignId('monitored_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('metrics')->nullable()->comment('Flexible progress metrics');
            $table->timestamps();
            
            // Indexes for performance
            // Note: morphs() already creates index on progressionable_type and progressionable_id
            $table->index(['patient_id', 'progress_date']);
            $table->index('monitored_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practice_progressions');
    }
};
