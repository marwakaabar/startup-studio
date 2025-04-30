<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Reaction;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'reactable_type' => 'required|in:Post,Comment',
            'reactable_id' => 'required|integer',
            'reaction' => 'required|string'
        ]);

        $model = $request->reactable_type === 'Post' ? Post::class : Comment::class;
        $reactable = $model::findOrFail($request->reactable_id);

        $existingReaction = $reactable->reactions()
            ->where('user_id', auth()->id())
            ->first();

        if ($existingReaction) {
            if ($existingReaction->type === $request->reaction) {
                $existingReaction->delete();
                $message = 'Reaction removed';
            } else {
                $existingReaction->update(['type' => $request->reaction]);
                $message = 'Reaction updated';
            }
        } else {
            $reactable->reactions()->create([
                'user_id' => auth()->id(),
                'type' => $request->reaction
            ]);
            $message = 'Reaction added';
        }

        $reactionCounts = $reactable->reactions()
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type');

        return response()->json([
            'message' => $message,
            'reactions' => $reactionCounts
        ]);
    }
}
