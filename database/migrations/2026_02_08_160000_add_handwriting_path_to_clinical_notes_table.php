<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinical_notes', function (Blueprint $table) {
            $table->string('handwriting_path', 500)->nullable()->after('ai_summary');
        });
    }

    public function down(): void
    {
        Schema::table('clinical_notes', function (Blueprint $table) {
            $table->dropColumn('handwriting_path');
        });
    }
};
