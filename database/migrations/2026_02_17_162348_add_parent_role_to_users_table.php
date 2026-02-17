<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add 'parent' and 'others' to users.role enum if it is an enum.
     * If role is string (e.g. from update_users_table_for_mysql), no change needed.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $column = DB::selectOne("
            SELECT COLUMN_TYPE FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'users'
            AND COLUMN_NAME = 'role'
        ");

        if (!$column || !str_contains($column->COLUMN_TYPE, 'enum')) {
            return; // role is string or doesn't exist
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','doctor','assistant','patient','parent','others') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $column = DB::selectOne("
            SELECT COLUMN_TYPE FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'users'
            AND COLUMN_NAME = 'role'
        ");

        if (!$column || !str_contains($column->COLUMN_TYPE, 'enum')) {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','doctor','assistant','patient') NULL");
    }
};
