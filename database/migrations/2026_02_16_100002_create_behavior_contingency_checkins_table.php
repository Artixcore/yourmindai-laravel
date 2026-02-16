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
        Schema::create('behavior_contingency_checkins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('plan_item_id');
            $table->date('date');
            $table->boolean('followed');
            $table->text('client_note')->nullable();
            $table->unsignedBigInteger('reviewer_id')->nullable();
            $table->text('reviewer_note')->nullable();
            $table->text('applied_reward')->nullable();
            $table->text('applied_punishment')->nullable();
            $table->timestamps();

            $table->foreign('plan_id')->references('id')->on('behavior_contingency_plans')->onDelete('cascade');
            $table->foreign('plan_item_id')->references('id')->on('behavior_contingency_plan_items')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('set null');
            $table->unique(['plan_item_id', 'date']);
            $table->index('plan_id');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('behavior_contingency_checkins');
    }
};
