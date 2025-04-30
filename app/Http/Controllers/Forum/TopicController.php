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

        $posts = $topic->posts()
            ->with(['user.coach', 'user.startup', 'user.investisseur', 'comments.user'])
            ->paginate(10);
        
        // Charger les hashtags du forum associé au topic
        $hashtags = $topic->forum->hashtags()->get();
        
        // Ajouter l'image de l'utilisateur du topic
        $topic->user_image = $this->getUserProfileImage($topic->user);

        // Ajouter les images des utilisateurs pour chaque post et les commentaires
        foreach ($posts as $post) {
            $user = $post->user;
            $post->user_image = $this->getUserProfileImage($user);
            $post->topic_id = $topic->id; // Ajout de l'ID du topic
            $post->topic_user_id = $topic->user_id; // Ajout de l'ID de l'auteur du topic
            
            // S'assurer que is_best_answer est chargé
            $post->is_best_answer = (bool) $post->is_best_answer;

            // Ajouter l'image de l'utilisateur pour chaque commentaire
            foreach ($post->comments as $comment) {
                $comment->image = $this->getUserProfileImage($comment->user);
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

    public function index(Request $request, Forum $forum)
    {
        $filter = $request->input('filter', 'all');
        $query = $forum->topics()->with(['user', 'posts']); // Eager loading des relations

        switch ($filter) {
            case 'recent':
                $query->orderBy('created_at', 'desc');
                break;
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'unanswered':
                $query->whereDoesntHave('posts');
                break;
            default:
                $query->latest();
        }

        $topics = $query->paginate(10);
        
        // Transformer les données pour inclure last_post
        $topics->getCollection()->transform(function ($topic) {
            $topic->last_post = $topic->posts()->latest()->with('user')->first();
            return $topic;
        });

        return response()->json([
            'topics' => $topics->items(),
            'currentPage' => $topics->currentPage(),
            'lastPage' => $topics->lastPage(),
            'total' => $topics->total()
        ]);
    }

    /**
     * Récupère l'image de l'utilisateur connecté.
     */
    public function getUserImage()
    {
        if (auth()->check()) {
            $user = auth()->user();
            return response()->json(['image' => $this->getUserProfileImage($user)]);
        }

        return response()->json(['image' => "https://eu.ui-avatars.com/api/?background=D43347&color=fff&bold=true&name=User"]);
    }

    /**
     * Get user profile image with S3 temporary URL
     * 
     * @param \App\Models\User $user
     * @return string
     */
    private function getUserProfileImage($user)
    {
        $imagePath = null;

        if ($user->isCoach() && $user->coach->profile_image) {
            $imagePath = 'images/' . $user->coach->profile_image;
        } elseif ($user->isStartup() && $user->startup->logo_startup) {
            $imagePath = 'images/' . $user->startup->logo_startup;
        } elseif ($user->isInvestisseur() && $user->investisseur->profile_image) {
            $imagePath = 'images/' . $user->investisseur->profile_image;
        } elseif ($user->isAdmin() && $user->admin->profile_image) {
            $imagePath = $user->admin->profile_image;
        }

        if ($imagePath) {
            try {
                return Storage::disk('s3')->temporaryUrl($imagePath, now()->addMinutes(30));
            } catch (\Exception $e) {
                \Log::error('S3 temporary URL error: ' . $e->getMessage());
            }
        }

        // Fallback to default image if no image exists or error occurs
        return "https://eu.ui-avatars.com/api/?background=D43347&color=fff&bold=true&name=" . urlencode($user->name);
    }
}
