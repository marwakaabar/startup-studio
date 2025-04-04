<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Forum extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'user_id',
        'visibility',
        'image',
        'category',
        'views_count',
    ];

    //la relation avec user 
    public function user(){
        return $this->belongsTo(User::class);
    }

    //ici la relation avec les topics
    public function topics(){
        return $this->hasMany(Topic::class);
    }

    public function hashtags(){
        return $this->morphToMany(Hashtag::class,'hashtagable');
    }

    //accesseur pour le comptage des topics.
    public function getTopicsCountAttribute()
    {
        return $this->topics()->count();
    }

    public function incrementViewCount()
    {
        $this->increment('views_count');
    }

}
