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
        // Article Comments
        Schema::create('article_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('parent_id')->nullable()->constrained('article_comments')->onDelete('cascade');
            $table->string('name', 100)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('comment');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
            
            $table->index('article_id');
            $table->index('status');
        });
        
        // Article Likes
        Schema::create('article_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('ip_address', 45);
            $table->timestamps();
            
            $table->index('article_id');
        });
        
        // Article Views
        Schema::create('article_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->datetime('viewed_at');
            $table->timestamps();
            
            $table->index('article_id');
            $table->index('viewed_at');
        });
        
        // Article Earnings
        Schema::create('article_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('views_count')->default(0);
            $table->decimal('earnings_amount', 10, 2)->default(0);
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('status', ['pending', 'calculated', 'paid'])->default('pending');
            $table->datetime('paid_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['period_start', 'period_end']);
        });
        
        // Article Media
        Schema::create('article_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->nullable()->constrained('articles')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('filename', 255);
            $table->string('original_filename', 255);
            $table->string('file_path', 500);
            $table->string('mime_type', 100);
            $table->integer('file_size');
            $table->timestamps();
            
            $table->index('article_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_media');
        Schema::dropIfExists('article_earnings');
        Schema::dropIfExists('article_views');
        Schema::dropIfExists('article_likes');
        Schema::dropIfExists('article_comments');
    }
};
