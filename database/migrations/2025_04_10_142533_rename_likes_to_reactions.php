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
        // Supprimer d'abord la table si elle existe
        Schema::dropIfExists('likes');

        // Recréer la table avec la structure correcte
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('reactionable'); // Ceci créera reactionable_type et reactionable_id
            $table->string('type')->default('like');
            $table->timestamps();
            
            // Ajouter un index unique pour éviter les doublons
            $table->unique(['user_id', 'reactionable_id', 'reactionable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reactions');

    }
};
