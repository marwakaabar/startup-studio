<?php

namespace App\Http\Controllers\Forum;

use App\Models\Topic;
use App\Models\Forum;
use App\Models\Hashtag;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
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

        // Gestion des hashtags
        if ($request->has('tags')) {
            $tags = collect(json_decode($request->tags))->unique();
            foreach ($tags as $tagName) {
                $hashtag = Hashtag::firstOrCreate(['name' => $tagName]);
                $topic->hashtags()->attach($hashtag->id);
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Topic créé avec succès',
                'redirect' => route('forums.show', $forum->id)
            ]);
        }

        return redirect()->route('forums.show', $forum->id)
            ->with('success', 'Topic créé avec succès');
    }

    public function create(Forum $forum)
    {
        return view('startup.Forum.create', ['forum' => $forum]);
    }

    public function show(Topic $topic)
    {
        $currentUser = Auth::user();
        // Charger les relations nécessaires
        $currentUser->load(['coach', 'startup', 'investisseur', 'admin']);
        
        // Ajouter les propriétés de rôle
        $currentUser->isCoach = $currentUser->isCoach();
        $currentUser->isStartup = $currentUser->isStartup();
        $currentUser->isInvestisseur = $currentUser->isInvestisseur();
        $currentUser->isAdmin = $currentUser->isAdmin();

        // S'assurer que l'image de profil est accessible
        $currentUser->profile_image = $currentUser->getProfileImageAttribute();

        // Increment view count
        $sessionKey = 'topic_viewed_' . $topic->id;
        if (!session()->has($sessionKey)) {
            $topic->incrementViewCount();
            session()->put($sessionKey, true);
        }

        // Load relationships
        $topic->load(['user', 'forum']);
        $posts = $topic->posts()
            ->with(['user', 'comments.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Add user images
        $topic->user_image = $this->getUserProfileImage($topic->user);
        foreach ($posts as $post) {
            $post->user_image = $this->getUserProfileImage($post->user);
            foreach ($post->comments as $comment) {
                $comment->user_image = $this->getUserProfileImage($comment->user);
            }
        }

        if (request()->wantsJson()) {
            return response()->json([
                'topic' => $topic,
                'posts' => $posts,
                'isFollowing' => auth()->check() ? $topic->subscriptions()->where('user_id', auth()->id())->exists() : false
            ]);
        }

        return view('startup.Forum.showTopic', [
            'topic' => $topic,
            'posts' => $posts,
            'currentUser' => $currentUser
        ]);
    }

    public function index(Request $request, Forum $forum)
    {
        $filter = $request->input('filter', 'all');
        $query = $forum->topics()
            ->with(['user', 'posts.reactions', 'posts.comments.reactions', 'posts.user', 'posts.comments.user']);

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
        
        // Transformer les données pour inclure les statistiques et les avatars
        $topics->getCollection()->transform(function ($topic) {
            // Récupérer les 6 derniers utilisateurs uniques qui ont interagi
            $userIds = collect();
            
            // Ajouter les utilisateurs des posts
            $topic->posts->each(function ($post) use ($userIds) {
                $userIds->push($post->user_id);
                // Ajouter les utilisateurs des commentaires
                $post->comments->each(function ($comment) use ($userIds) {
                    $userIds->push($comment->user_id);
                });
            });

            // Prendre les 6 derniers utilisateurs uniques
            $recentUsers = $userIds->unique()
                ->take(6)
                ->map(function ($userId) {
                    $user = \App\Models\User::find($userId);
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'image' => $this->getUserProfileImage($user)
                    ];
                });

            // Calculer le nombre total de likes
            $totalLikes = $topic->posts->sum(function ($post) {
                return $post->reactions->where('type', 'like')->count() +
                       $post->comments->sum(function ($comment) {
                           return $comment->reactions->where('type', 'like')->count();
                       });
            });

            $topic->recent_users = $recentUsers;
            $topic->total_likes = $totalLikes;
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
