<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE patient_devices MODIFY device_type ENUM('mobile','tablet','desktop','wearable','smartwatch','other') DEFAULT 'mobile'");
        } else {
            Schema::table('patient_devices', function (Blueprint $table) {
                $table->string('device_type', 50)->default('mobile')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE patient_devices MODIFY device_type ENUM('mobile','tablet','desktop') DEFAULT 'mobile'");
        }
    }
};
