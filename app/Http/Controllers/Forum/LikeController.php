<?php

namespace App\Http\Controllers\Forum;

use App\Models\Like;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class LikeController extends Controller
{
    public function toggleLike(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $user = auth()->user();
            $type = $request->input('type');
            $id = $request->input('id');

            if (!in_array($type, ['post', 'comment'])) {
                return response()->json(['message' => 'Type de like invalide'], 400);
            }

            $model = $type === 'post' ? Post::class : Comment::class;
            $likeable = $model::find($id);

            if (!$likeable) {
                return response()->json(['message' => 'Element non trouvé'], 404);
            }

            $existingLike = Like::where([
                'user_id' => $user->id,
                'likeable_type' => $model,
                'likeable_id' => $id
            ])->first();

            if ($existingLike) {
                $existingLike->delete();
                DB::commit();
                
                return response()->json([
                    'message' => 'Like supprimé',
                    'likes_count' => $likeable->likes()->count(),
                    'is_liked' => false
                ]);
            }

            $like = new Like([
                'user_id' => $user->id,
                'likeable_type' => $model,
                'likeable_id' => $id
            ]);
            
            $likeable->likes()->save($like);
            DB::commit();

            return response()->json([
                'message' => 'Like ajouté',
                'likes_count' => $likeable->likes()->count(),
                'is_liked' => true
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Une erreur est survenue',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
