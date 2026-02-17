<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add verifier_role and remarks to task_verifications for supervision support.
     */
    public function up(): void
    {
        Schema::table('task_verifications', function (Blueprint $table) {
            $table->string('verifier_role', 20)->default('parent')->after('parent_user_id');
            $table->text('remarks')->nullable()->after('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_verifications', function (Blueprint $table) {
            $table->dropColumn(['verifier_role', 'remarks']);
        });
    }
};
