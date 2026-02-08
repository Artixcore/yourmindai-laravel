<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('session_mode', 20)->nullable();
            $table->decimal('booking_fee', 10, 2)->default(0);
            $table->string('payment_status', 20)->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['session_mode', 'booking_fee', 'payment_status', 'paid_at', 'cancelled_at']);
        });
    }
};
