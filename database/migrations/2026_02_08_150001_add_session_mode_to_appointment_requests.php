<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointment_requests', function (Blueprint $table) {
            $table->string('session_mode', 20)->nullable()->after('notes'); // in_person, online
        });
    }

    public function down(): void
    {
        Schema::table('appointment_requests', function (Blueprint $table) {
            $table->dropColumn('session_mode');
        });
    }
};
