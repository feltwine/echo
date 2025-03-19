<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('votables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vote_id')->constrained()->onDelete('cascade');
            $table->morphs('votable'); // Adds votable_id and votable_type columns
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votables');
    }
};
