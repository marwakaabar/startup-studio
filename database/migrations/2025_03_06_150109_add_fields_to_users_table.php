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
        Schema::table('users', function (Blueprint $table) {
            // Champ pour la visibilité de l'investisseur (nullable)
            $table->enum('visibility', ['public', 'private'])->default('public');
             
            $table->string('image')->nullable();

            // startup 
            $table->string('domain_name')->nullable();

            // Coach
            $table->string('specialty')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['investor_name', 'visibility', 'image', 'domain_name', 'specialty']);
        });
    }
};
