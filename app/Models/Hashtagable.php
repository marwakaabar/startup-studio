<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hashtagable extends Pivot
{

    use HasFactory;

    protected $fillable = [
        'hashtag_id', 
        'hashtagable_id', 
        'hashtagable_type'
    ];

}
