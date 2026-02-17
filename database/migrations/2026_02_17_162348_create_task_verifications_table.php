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
        Schema::create('task_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('parent_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('verified_at')->useCurrent();
            $table->timestamps();

            $table->unique(['task_id', 'parent_user_id']);
            $table->index('parent_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_verifications');
    }
};
