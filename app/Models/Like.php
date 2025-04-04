<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Like extends Model
{  
    use HasFactory;

    protected $fillable = [
        'user_id',
        'likeable_type',
        'likeable_id'
    ];

    public function likeable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeIsLikedBy($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
