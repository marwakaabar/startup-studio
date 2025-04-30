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
        Schema::create('moderated_contents', function (Blueprint $table) {
            $table->id();
            $table->string('content_type'); // 'Post', 'Comment', etc.
            $table->unsignedBigInteger('content_id');
            $table->text('original_content');
            $table->text('moderated_content')->nullable();
            $table->boolean('is_toxic')->default(false);
            $table->json('toxicity_scores')->nullable();
            $table->string('most_toxic_category')->nullable();
            $table->float('most_toxic_score')->nullable();
            $table->string('language')->default('fr');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('moderation_action')->default('none'); // 'none', 'moderate', 'hide', 'block'
            $table->string('severity')->default('none'); // 'none', 'low', 'medium', 'high', 'extreme'
            $table->boolean('is_hidden')->default(false);
            $table->boolean('admin_reviewed')->default(false);
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('admin_decision')->nullable(); // 'restore', 'keep_modified', 'keep_hidden', 'delete'
            $table->timestamps();
            
            // Index pour améliorer les performances des requêtes polymorphiques
            $table->index(['content_type', 'content_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moderated_contents');
    }
}; 