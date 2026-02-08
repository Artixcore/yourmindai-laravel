<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('description');
            $table->date('end_date')->nullable()->after('start_date');
            $table->unsignedSmallInteger('frequency_per_day')->nullable()->comment('Times per day')->after('end_date');
            $table->unsignedSmallInteger('duration_minutes')->nullable()->comment('Duration per time in minutes')->after('frequency_per_day');
        });
    }

    public function down(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'frequency_per_day', 'duration_minutes']);
        });
    }
};
