<?php

namespace App\Http\Controllers\Forum;

use App\Models\Topic;
use App\Models\Forum;
use App\Models\Hashtag;
use Illuminate\Support\Facades\Storage;
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

        // Gestion des hashtags avec vérification des doublons
        if ($request->has('tags')) {
            $tags = collect(json_decode($request->tags))->unique(); // Supprime les doublons dans la requête
            foreach ($tags as $tagName) {
                // Vérifie si le hashtag existe déjà dans le forum
                if (!$forum->hashtags()->where('name', $tagName)->exists()) {
                    $hashtag = Hashtag::firstOrCreate(
                        ['name' => $tagName],
                        ['name' => $tagName]
                    );
                    $topic->hashtags()->attach($hashtag->id);
                }
            }
        }

        return redirect()->route('forums.show', $forum->id);
    }

    public function create(Forum $forum)
    {
        return Inertia::render('Topics/Create', ['forum' => $forum]);
    }

    public function show(Topic $topic)
    {
        // Incrémenter le compteur de vues uniquement si l'utilisateur n'a pas déjà vu le topic
        $sessionKey = 'topic_viewed_' . $topic->id;
        if (!session()->has($sessionKey)) {
            $topic->incrementViewCount();
            session()->put($sessionKey, true);
        }

        $posts = $topic->posts()->with(['user.coach', 'user.startup', 'user.investisseur'])->paginate(10);
        
        // Charger les hashtags du forum associé au topic
        $hashtags = $topic->forum->hashtags()->get();
        
        // Ajouter l'image de l'utilisateur du topic
        $topicUser = $topic->user;
        if ($topicUser->isCoach()) {
            $topic->user_image = $topicUser->coach->profile_image ? Storage::url('images/' . $topicUser->coach->profile_image) : asset('images/placeholder.jpg');
        } elseif ($topicUser->isStartup()) {
            $topic->user_image = $topicUser->startup->logo_startup ? Storage::url('images/' . $topicUser->startup->logo_startup) : asset('images/placeholder.jpg');
        } elseif ($topicUser->isInvestisseur()) {
            $topic->user_image = $topicUser->investisseur->profile_image ? Storage::url('images/' . $topicUser->investisseur->profile_image) : asset('images/placeholder.jpg');
        } elseif ($topicUser->isAdmin()) {
            $topic->user_image = $topicUser->admin->profile_image ? Storage::url($topicUser->admin->profile_image) : asset('images/placeholder.jpg');
        } else {
            $topic->user_image = asset('images/placeholder.jpg');
        }

        // Ajouter les images des utilisateurs pour chaque post
        foreach ($posts as $post) {
            $user = $post->user;
            $post->user_image = null;
            $post->topic_id = $topic->id; // Ajout de l'ID du topic
            $post->topic_user_id = $topic->user_id; // Ajout de l'ID de l'auteur du topic
            
            // S'assurer que is_best_answer est chargé
            $post->is_best_answer = (bool) $post->is_best_answer;

            if ($user->isCoach()) {
                $post->user_image = $user->coach->profile_image ? Storage::url('images/' . $user->coach->profile_image) : asset('images/placeholder.jpg');
            } elseif ($user->isStartup()) {
                $post->user_image = $user->startup->logo_startup ? Storage::url('images/' . $user->startup->logo_startup) : asset('images/placeholder.jpg');
            } elseif ($user->isInvestisseur()) {
                $post->user_image = $user->investisseur->profile_image ? Storage::url('images/' . $user->investisseur->profile_image) : asset('images/placeholder.jpg');
            } elseif ($user->isAdmin()) {
                $post->user_image = $user->admin->profile_image ? Storage::url($user->admin->profile_image) : asset('images/placeholder.jpg');
            } else {
                $post->user_image = asset('images/placeholder.jpg');
            }
        }

        return Inertia::render('Topics/Show', [
            'topic' => $topic->load('user'),
            'posts' => $posts->items(),
            'total' => $posts->total(),
            'currentPage' => $posts->currentPage(),
            'lastPage' => $posts->lastPage(),
            'hashtags' => $hashtags, // Ajout des hashtags
        ]);
    }



    /**
     * Récupère l'image de l'utilisateur connecté.
     */
    public function getUserImage()
    {
        if (auth()->check()) {
            $user = auth()->user();
            $userImage = asset('images/placeholder.jpg'); // Image par défaut

            if ($user->isCoach()) {
                $userImage = $user->coach->profile_image 
                    ? Storage::url('images/' . $user->coach->profile_image) 
                    : asset('images/placeholder.jpg');
            } elseif ($user->isStartup()) {
                $userImage = $user->startup->logo_startup 
                    ? Storage::url('images/' . $user->startup->logo_startup) 
                    : asset('images/placeholder.jpg');
            } elseif ($user->isInvestisseur()) {
                $userImage = $user->investisseur->profile_image 
                    ? Storage::url('images/' . $user->investisseur->profile_image) 
                    : asset('images/placeholder.jpg');
            } elseif ($user->isAdmin()) {
                $userImage = $user->admin->profile_image 
                    ? Storage::url($user->admin->profile_image) 
                    : asset('images/placeholder.jpg');
            }

            return response()->json(['image' => $userImage]);
        }

        return response()->json(['image' => asset('images/placeholder.jpg')]);
    }
}
