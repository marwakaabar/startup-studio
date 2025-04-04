<?php

namespace App\Http\Controllers\Forum;

use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    private function getUserImage($user)
    {
        if ($user->isCoach()) {
            return $user->coach->profile_image ? Storage::url('images/' . $user->coach->profile_image) : asset('images/placeholder.jpg');
        } elseif ($user->isStartup()) {
            return $user->startup->logo_startup ? Storage::url('images/' . $user->startup->logo_startup) : asset('images/placeholder.jpg');
        } elseif ($user->isInvestisseur()) {
            return $user->investisseur->profile_image ? Storage::url('images/' . $user->investisseur->profile_image) : asset('images/placeholder.jpg');
        } elseif ($user->isAdmin()) {
            return $user->admin->profile_image ? Storage::url($user->admin->profile_image) : asset('images/placeholder.jpg');
        }
        return asset('images/placeholder.jpg');
    }

    private function preparePostData($post)
    {
        $post->load('user', 'topic');
        $post->topic_user_id = $post->topic->user_id;
        $post->user_image = $this->getUserImage($post->user);
        return $post;
    }

    public function store(Request $request, Topic $topic)
    {
        $request->validate(['content' => 'required|string']);
        $post = $topic->posts()->create([
            'content' => $request->content,
            'user_id' => auth()->id()
        ]);
        return response()->json(['success' => true, 'post' => $this->preparePostData($post)]);
    } 

    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Vous n\'êtes pas autorisé à modifier ce post.'], 403);
        }

        $request->validate([
            'content' => 'required|string'
        ]);

        $post->update([
            'content' => $request->content
        ]);

        return response()->json([
            'success' => true,
            'post' => $this->preparePostData($post)
        ]);
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Vous n\'êtes pas autorisé à supprimer ce post.'], 403);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post supprimé avec succès.'
        ]);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $image = $request->file('image');
        $path = $image->store('posts/images', 'public');

        return response()->json([
            'success' => true,
            'url' => Storage::url($path)
        ]);
    }

    public function toggleBestAnswer(Post $post)
    {
        $topic = $post->topic;
        
        if ($topic->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        try {
            \DB::transaction(function() use ($topic, $post) {
                // Si ce post est déjà la meilleure réponse, on le désactive simplement
                if ($post->is_best_answer) {
                    $post->update(['is_best_answer' => false]);
                    return;
                }

                // Sinon, on retire le statut des autres posts et on marque celui-ci
                $topic->posts()->where('is_best_answer', true)->update(['is_best_answer' => false]);
                $post->update(['is_best_answer' => true]);
            });

            return response()->json([
                'success' => true,
                'is_best_answer' => $post->fresh()->is_best_answer,
                'message' => $post->is_best_answer ? 'Marqué comme meilleure réponse' : 'Statut de meilleure réponse retiré'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }
}
