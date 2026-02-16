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
        Schema::table('patient_devices', function (Blueprint $table) {
            $table->string('device_source', 50)->default('app_registered')->after('device_identifier')
                ->comment('app_registered, manual');
            $table->text('notes')->nullable()->after('app_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_devices', function (Blueprint $table) {
            $table->dropColumn(['device_source', 'notes']);
        });
    }
};
