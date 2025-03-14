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

    public function forums()
    {
        return $this->morphedByMany(Forum::class, 'hashtagable');
    }

    public function topics()
    {
        return $this->morphedByMany(Topic::class, 'hashtagable');
    }

    public function posts()
    {
        return $this->morphedByMany(Post::class, 'hashtagable');
    }
}
