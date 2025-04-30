<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content', 
        'user_id', 
        'post_id'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactionable');
    }

    protected $appends = ['reactions_count', 'user_reaction'];

    public function getReactionsCountAttribute()
    {
        return $this->reactions()
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type');
    }

    public function getUserReactionAttribute()
    {
        return $this->reactions()
            ->where('user_id', auth()->id())
            ->value('type');
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }
    
    // Relation pour la modération de contenu
    public function moderatedContents()
    {
        return $this->morphMany(ModeratedContent::class, 'moderatable');
    }

    // Méthode pour vérifier si le contenu est toxique
    public function checkModeration()
    {
        return $this->moderatedContents()->latest()->first();
    }
}
