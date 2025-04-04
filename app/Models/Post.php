<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable=[
        'content',
        'user_id',
        'topic_id',
        'is_best_answer'
    ];

    protected $casts = [
        'images' => 'array',
        'is_best_answer' => 'boolean'
    ];

    //relation avec l'utilisateur
    public function user(){
        return $this->belongsTo(User::class);
    }

    //relation avec le topic 
    public function  topic(){
        return $this->belongsTo(Topic::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    protected $appends = ['likes_count', 'is_liked'];

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function getIsLikedAttribute()
    {
        return $this->likes()->isLikedBy(auth()->id())->exists();
    }

    public function hashtags()
    {
        return $this->morphToMany(Hashtag::class, 'hashtagable');
    }


}
