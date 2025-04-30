<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reactionable_id',
        'reactionable_type',
        'type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reactionable()
    {
        return $this->morphTo();
    }

    public function scopeIsReactedBy($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
