<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hashtag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    // Assurez-vous que les noms de hashtags sont uniques
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($hashtag) {
            $hashtag->name = strtolower($hashtag->name);
        });
    }

    public function forums()
    {
        return $this->morphedByMany(Forum::class, 'hashtagable')->distinct();
    }

    public function topics()
    {
        return $this->morphedByMany(Topic::class, 'hashtagable')->distinct();
    }

    public function posts()
    {
        return $this->morphedByMany(Post::class, 'hashtagable')->distinct();
    }
}
