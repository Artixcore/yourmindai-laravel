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
        // Article-Category pivot
        Schema::create('article_category_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('article_categories')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['article_id', 'category_id']);
        });
        
        // Article-Tag pivot
        Schema::create('article_tag_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
            $table->foreignId('tag_id')->constrained('article_tags')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['article_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_tag_pivot');
        Schema::dropIfExists('article_category_pivot');
    }
};
