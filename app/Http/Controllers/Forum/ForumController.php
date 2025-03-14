<?php

namespace App\Http\Controllers\Forum;

use App\Models\Forum;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;


class ForumController extends Controller
{

    public function index()
    {
        return Inertia::render('Forums/Index', [
            'forums' => Forum::with('user')->latest()->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Forums/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:forums',
            'description' => 'nullable|string',
            'visibility' => 'required|in:public,private',
        ]);

        $forum = Forum::create([
            'name' => $request->name,
            'description' => $request->description,
            'visibility' => $request->visibility,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('forums.show',$form->id);
    }

    public function show(Forum $forum)
    {
        return Inertia::render('Forums/Show', [
            'forum' => $forum->load('topics.user')
        ]);
    }

}
