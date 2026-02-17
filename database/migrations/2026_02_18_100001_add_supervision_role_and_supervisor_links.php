<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            $column = DB::selectOne("
                SELECT COLUMN_TYPE FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'users'
                AND COLUMN_NAME = 'role'
            ");

            if ($column && str_contains((string) $column->COLUMN_TYPE, 'enum')) {
                DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','doctor','assistant','patient','parent','others','supervision') NULL");
            }
        }

        Schema::create('supervisor_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supervisor_id');
            $table->unsignedBigInteger('patient_id');
            $table->timestamps();

            $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patient_profiles')->onDelete('cascade');
            $table->unique(['supervisor_id', 'patient_id']);
            $table->index('supervisor_id');
            $table->index('patient_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supervisor_links');

        if (DB::getDriverName() === 'mysql') {
            $column = DB::selectOne("
                SELECT COLUMN_TYPE FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'users'
                AND COLUMN_NAME = 'role'
            ");

            if ($column && str_contains((string) $column->COLUMN_TYPE, 'enum')) {
                DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','doctor','assistant','patient','parent','others') NULL");
            }
        }
    }
};
