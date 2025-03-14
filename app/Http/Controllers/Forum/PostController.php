<?php

namespace App\Http\Controllers\Forum;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    public function store(Request $request, Topic $topic)
    {
        $request->validate(['content' => 'required|string']);

        $post = $topic->posts()->create([
            'content' => $request->content,
            'user_id' => auth()->id()
        ]);

        return back();
    } 
}
