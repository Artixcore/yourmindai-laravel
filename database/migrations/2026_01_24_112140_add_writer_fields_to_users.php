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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_writer')->default(false)->after('status');
            $table->text('writer_bio')->nullable()->after('is_writer');
            $table->string('writer_avatar')->nullable()->after('writer_bio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_writer', 'writer_bio', 'writer_avatar']);
        });
    }
};
