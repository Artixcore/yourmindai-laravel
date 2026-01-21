<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add username unique index if column exists but no unique constraint
            if (Schema::hasColumn('users', 'username')) {
                // Check if unique index doesn't exist using raw SQL
                $indexExists = DB::select("
                    SELECT COUNT(*) as count
                    FROM information_schema.STATISTICS
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'users'
                    AND INDEX_NAME = 'users_username_unique'
                ");
                
                if (empty($indexExists) || $indexExists[0]->count == 0) {
                    $table->unique('username');
                }
            } else {
                $table->string('username')->unique()->nullable()->after('email');
            }

            // Add fields if they don't exist
            if (!Schema::hasColumn('users', 'full_name')) {
                $table->string('full_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'avatar_path')) {
                $table->string('avatar_path')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('role');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'doctor', 'assistant', 'patient'])->default('patient')->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('users', 'avatar_path')) {
                $table->dropColumn('avatar_path');
            }
            if (Schema::hasColumn('users', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('users', 'full_name')) {
                $table->dropColumn('full_name');
            }
            // Don't drop username or role as they may be used elsewhere
        });
    }
};
