<?php

namespace App\Http\Controllers\Forum;

use App\Models\Hashtag;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class HashtagController extends Controller
{
    public function index()
    {
        $hashtags = Hashtag::all();
        return response()->json($hashtags);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $hashtags = Hashtag::where('name', 'LIKE', '%' . $query . '%')->get();

        return response()->json($hashtags);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:hashtags',
        ]);

        $hashtag = Hashtag::create([
            'name' => $request->name,
        ]);

        return response()->json($hashtag, 201);
    }

    public function destroy(Hashtag $hashtag)
    {
        $hashtag->delete();

        return response()->json(['message' => 'Hashtag supprimé']);
    }
}
