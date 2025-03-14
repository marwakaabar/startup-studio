<?php

namespace App\Http\Controllers\Forum;

use App\Models\Topic;
use App\Models\Forum;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class TopicController extends Controller
{
    public function store(Request $request, Forum $forum)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $topic = $forum->topics()->create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => auth()->id(),
            'is_thread' => true
        ]);

        return redirect()->route('forums.show', $forum->id);
    }

    public function show(Topic $topic)
    {
        return Inertia::render('Topics/Show', [
            'topic' => $topic->load('posts.user')
        ]);
    }
}
