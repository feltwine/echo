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
        Schema::create('hubs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('followers_count')->default(0);

            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->fullText(['name', 'description']);
            $table->enum('status', ['public', 'private'])->default('private');

            $table->string('avatar_path')->nullable();
            $table->string('background_path')->nullable();
            $table->string('background_color')->default('#721378');

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('moderators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hub_id')->constrained('hubs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hubs');
        Schema::dropIfExists('moderators');
    }
};
