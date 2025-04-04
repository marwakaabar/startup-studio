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
        Schema::create('hashtags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Table pivot pour lier les hashtags avec forums, topics et posts
        Schema::create('hashtagables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hashtag_id')->constrained()->onDelete('cascade');
            $table->morphs('hashtagable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hashtagables');
        Schema::dropIfExists('hashtags');
    }
};
