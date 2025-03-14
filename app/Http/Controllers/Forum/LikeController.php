<?php

namespace App\Http\Controllers\Forum;

use App\Models\Like;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Controllers\Controller;

class LikeController extends Controller
{
    public function toggleLike(Post $post)
    {
        $user = auth()->user();

        $existingLike = $post->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            $existingLike->delete();
            return response()->json(['message' => 'Like supprimé']);
        } else {
            $post->likes()->create(['user_id' => $user->id]);
            return response()->json(['message' => 'Like ajouté']);
        }
    }
}
