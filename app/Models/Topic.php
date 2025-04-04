<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'forum_id',
        'is_thread',
        'views_count',
    ];

    protected $appends = ['posts_count', 'last_post'];

    //ici la relation avec le forum
    public function forum(){
        return $this->belongsTo(Forum::class);
    }

    //ici la relation avec le user
    public function user(){
        return $this->belongsTo(User::class);
    }

    //la relation avec les posts 
    public function posts(){
        return $this->hasMany(Post::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
    
    public function hashtags()
    {
        return $this->morphToMany(Hashtag::class,'hashtagable');
    }

    public function getPostsCountAttribute()
    {
        return $this->posts()->count();
    }

    public function getLastPostAttribute()
    {
        return $this->posts()->latest()->with('user')->first();
    }

    public function incrementViewCount()
    {
        $this->increment('views_count');
    }
}
