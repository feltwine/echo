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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->integer('followers_count')->default(0);

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('hub_id')->constrained('hubs')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('body');
            $table->string('image')->nullable();
            $table->integer('vote_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');
            $table->text('body');
            $table->string('image')->nullable();
            $table->integer('vote_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->morphs('votable');
            $table->enum('value', [1, -1]);
            $table->unique(['user_id', 'votable_id', 'value']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('votes');
    }
};
