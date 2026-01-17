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
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->enum('status', ['new', 'in_progress', 'resolved'])->default('new')->after('message');
            $table->text('admin_notes')->nullable()->after('status');
            $table->timestamp('resolved_at')->nullable()->after('admin_notes');
            $table->unsignedBigInteger('resolved_by')->nullable()->after('resolved_at');
            
            $table->foreign('resolved_by')->references('id')->on('users')->onDelete('set null');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropForeign(['resolved_by']);
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'admin_notes', 'resolved_at', 'resolved_by']);
        });
    }
};
