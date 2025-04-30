<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModeratedContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_type',
        'content_id',
        'original_content',
        'moderated_content',
        'is_toxic',
        'toxicity_scores',
        'most_toxic_category',
        'most_toxic_score',
        'language',
        'user_id',
        'moderation_action',
        'admin_reviewed',
        'admin_notes'
    ];

    protected $casts = [
        'is_toxic' => 'boolean',
        'toxicity_scores' => 'array',
        'admin_reviewed' => 'boolean'
    ];

    // Relation polymorphique pour connecter à n'importe quel type de contenu
    public function moderatable()
    {
        return $this->morphTo();
    }

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 