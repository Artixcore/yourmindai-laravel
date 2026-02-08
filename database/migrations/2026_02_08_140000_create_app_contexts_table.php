<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_contexts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('The Application');
            $table->timestamps();
        });
        if (!\DB::table('app_contexts')->where('id', 1)->exists()) {
            \DB::table('app_contexts')->insert(['id' => 1, 'name' => 'The Application', 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('app_contexts');
    }
};
