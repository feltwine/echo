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
        Schema::create('hub_followers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('hub_id')->constrained('hubs')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('user_followers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('followed_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('follower_id')->constrained('users')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('post_followers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_hubs');
        Schema::dropIfExists('post_followers');
        Schema::dropIfExists('user_followers');
    }
};
