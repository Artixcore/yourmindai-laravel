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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('actor_user_id');
            $table->string('action'); // e.g., 'staff.created', 'report.generated'
            $table->string('entity_type'); // e.g., 'User', 'AiReport'
            $table->unsignedBigInteger('entity_id');
            $table->json('metadata')->nullable();
            $table->timestamp('created_at');

            $table->foreign('actor_user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes for performance
            $table->index('actor_user_id');
            $table->index('action');
            $table->index(['entity_type', 'entity_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
