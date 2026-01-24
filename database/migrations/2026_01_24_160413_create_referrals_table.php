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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient_profiles')->onDelete('cascade');
            $table->foreignId('referred_by')->constrained('users')->onDelete('cascade')->comment('Doctor making referral');
            $table->foreignId('referred_to')->nullable()->constrained('users')->onDelete('set null')->comment('Expert being referred to');
            $table->enum('referral_type', ['forward', 'back'])->default('forward');
            $table->foreignId('original_referral_id')->nullable()->constrained('referrals')->onDelete('set null')->comment('For back referrals');
            $table->string('specialty_needed')->nullable();
            $table->text('reason');
            $table->text('patient_history_summary')->nullable();
            $table->text('recommendations')->nullable();
            $table->string('report_file_path')->nullable();
            $table->json('attached_documents')->nullable();
            $table->enum('status', ['pending', 'accepted', 'in_progress', 'completed', 'declined'])->default('pending');
            $table->text('response_notes')->nullable();
            $table->timestamp('referred_at')->useCurrent();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['patient_id', 'status']);
            $table->index(['referred_by', 'status']);
            $table->index(['referred_to', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
