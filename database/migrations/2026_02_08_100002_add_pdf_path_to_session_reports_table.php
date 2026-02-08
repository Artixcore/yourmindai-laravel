<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('session_reports', function (Blueprint $table) {
            $table->string('pdf_path', 500)->nullable()->after('finalized_at');
        });
    }

    public function down(): void
    {
        Schema::table('session_reports', function (Blueprint $table) {
            $table->dropColumn('pdf_path');
        });
    }
};
