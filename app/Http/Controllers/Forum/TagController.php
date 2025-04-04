<?php

namespace App\Http\Controllers\Forum;

use App\Models\Hashtag;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class TagController extends Controller
{
    public function search(Request $request)
{
    $query = $request->input('query');
    $hashtags = Hashtag::where('name', 'LIKE', '%' . $query . '%')->get();

    return response()->json($hashtags);
}
}