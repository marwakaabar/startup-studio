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
        // Supprimer les colonnes inutiles de la table users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['image', 'domain_name', 'specialty', 'visibility']);
        });

        // Ajouter visibility à la table investisseurs
        Schema::table('investisseur', function (Blueprint $table) {
            $table->enum('visibility', ['public', 'private'])->default('public');
        });

        // Ajouter domain_name à la table startups
        Schema::table('startup', function (Blueprint $table) {
            $table->string('domain_name')->nullable();
        });

        // Ajouter specialty à la table mentors
        Schema::table('mentor', function (Blueprint $table) {
            $table->string('specialty')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Réajout des colonnes supprimées dans users
        Schema::table('users', function (Blueprint $table) {
            $table->string('image')->nullable();
            $table->string('domain_name')->nullable();
            $table->string('specialty')->nullable();
            $table->enum('visibility', ['public', 'private'])->default('public');
        });

        // Supprimer les colonnes ajoutées dans les autres tables
        Schema::table('investisseur', function (Blueprint $table) {
            $table->dropColumn('visibility');
        });

        Schema::table('startup', function (Blueprint $table) {
            $table->dropColumn('domain_name');
        });

        Schema::table('mentor', function (Blueprint $table) {
            $table->dropColumn('specialty');
        });
    }
};
