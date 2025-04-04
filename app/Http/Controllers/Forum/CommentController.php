<?php

namespace App\Http\Controllers\Forum;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{

    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = $post->comments()->create([
            'content' => $request->content,
            'user_id' => auth()->id(),
        ]);

        // Charger l'utilisateur et ses relations
        $comment->load('user');
        
        // Ajouter l'image de profil
        $user = $comment->user;
        $comment->image = null;

        if ($user->isCoach()) {
            $comment->image = $user->coach->profile_image ? Storage::url('images/' . $user->coach->profile_image) : asset('images/placeholder.jpg');
        } elseif ($user->isStartup()) {
            $comment->image = $user->startup->logo_startup ? Storage::url('images/' . $user->startup->logo_startup) : asset('images/placeholder.jpg');
        } elseif ($user->isInvestisseur()) {
            $comment->image = $user->investisseur->profile_image ? Storage::url('images/' . $user->investisseur->profile_image) : asset('images/placeholder.jpg');
        } elseif ($user->isAdmin()) {
            $comment->image = $user->admin->profile_image ? Storage::url($user->admin->profile_image) : asset('images/placeholder.jpg');
        } else {
            $comment->image = asset('images/placeholder.jpg');
        }

        return response()->json([
            'success' => true,
            'comment' => $comment
        ]);
    }

    public function update(Request $request, Comment $comment)
    {
        if ($comment->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Vous n\'êtes pas autorisé à modifier ce commentaire.'], 403);
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $comment->update([
            'content' => $request->content,
        ]);

        // Recharger le commentaire avec l'utilisateur
        $comment->load('user');
        
        // Ajouter l'image de profil
        $user = $comment->user;
        $comment->image = null;

        if ($user->isCoach()) {
            $comment->image = $user->coach->profile_image ? Storage::url('images/' . $user->coach->profile_image) : asset('images/placeholder.jpg');
        } elseif ($user->isStartup()) {
            $comment->image = $user->startup->logo_startup ? Storage::url('images/' . $user->startup->logo_startup) : asset('images/placeholder.jpg');
        } elseif ($user->isInvestisseur()) {
            $comment->image = $user->investisseur->profile_image ? Storage::url('images/' . $user->investisseur->profile_image) : asset('images/placeholder.jpg');
        } elseif ($user->isAdmin()) {
            $comment->image = $user->admin->profile_image ? Storage::url($user->admin->profile_image) : asset('images/placeholder.jpg');
        } else {
            $comment->image = asset('images/placeholder.jpg');
        }

        return response()->json(['success' => true, 'comment' => $comment]);
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Vous n\'êtes pas autorisé à supprimer ce commentaire.'], 403);
        }

        $comment->delete();

        return response()->json(['success' => true]);
    }


    public function index(Post $post)
{
    $comments = $post->comments()
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->get();

    foreach ($comments as $comment) {
        $user = $comment->user;
        $comment->image = null;

        if ($user->isCoach()) {
            $comment->image = $user->coach->profile_image ? Storage::url('images/' . $user->coach->profile_image) : asset('images/placeholder.jpg');
        } elseif ($user->isStartup()) {
            $comment->image = $user->startup->logo_startup ? Storage::url('images/' . $user->startup->logo_startup) : asset('images/placeholder.jpg');
        } elseif ($user->isInvestisseur()) {
            $comment->image = $user->investisseur->profile_image ? Storage::url('images/' . $user->investisseur->profile_image) : asset('images/placeholder.jpg');
        } elseif ($user->isAdmin()) {
            $comment->image = $user->admin->profile_image ? Storage::url($user->admin->profile_image) : asset('images/placeholder.jpg');
        } else {
            $comment->image = asset('images/placeholder.jpg'); 
        }
    }

    return response()->json($comments);
}


}
