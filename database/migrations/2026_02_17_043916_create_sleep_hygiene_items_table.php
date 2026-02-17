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
        Schema::create('sleep_hygiene_items', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_default')->default(true);
            $table->timestamps();
        });

        // Default sleep hygiene checklist items
        $defaults = [
            ['label' => 'No caffeine after 2pm', 'sort_order' => 0],
            ['label' => 'Consistent bedtime', 'sort_order' => 1],
            ['label' => 'Screen-free 1hr before bed', 'sort_order' => 2],
            ['label' => 'Dark room', 'sort_order' => 3],
            ['label' => 'Cool temperature', 'sort_order' => 4],
            ['label' => 'Relaxing wind-down routine', 'sort_order' => 5],
            ['label' => 'No heavy meals before bed', 'sort_order' => 6],
        ];
        foreach ($defaults as $item) {
            DB::table('sleep_hygiene_items')->insert(array_merge($item, [
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sleep_hygiene_items');
    }
};
