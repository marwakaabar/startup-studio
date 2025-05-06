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
            'reactable_type' => 'required|in:Post,Comment',
            'reactable_id' => 'required|integer',
            'type' => 'string|nullable',
            'reaction' => 'string|nullable'
        ]);

        $model = $request->reactable_type === 'Post' ? Post::class : Comment::class;
        $reactable = $model::findOrFail($request->reactable_id);

        $existingReaction = $reactable->reactions()
            ->where('user_id', auth()->id())
            ->first();

        // Si type est null, on supprime la réaction
        if (!$request->type) {
            if ($existingReaction) {
                $existingReaction->delete();
            }
        } else {
            if ($existingReaction) {
                if ($existingReaction->type === $request->type) {
                    $existingReaction->delete();
                } else {
                    $existingReaction->update(['type' => $request->type]);
                }
            } else {
                $reactable->reactions()->create([
                    'user_id' => auth()->id(),
                    'type' => $request->type
                ]);
            }
        }

        $reactionCounts = $reactable->reactions()
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        return response()->json([
            'success' => true,
            'reactions' => $reactionCounts
        ]);
    }
}
