<?php

namespace App\Http\Controllers\Forum;

use App\Models\Forum;
use App\Models\Hashtag;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ForumController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        
        $myForums = Forum::with(['user.coach', 'user.startup', 'user.investisseur', 'hashtags'])
            ->where('user_id', $userId)
            ->get();

        $otherForums = Forum::with(['user.coach', 'user.startup', 'user.investisseur', 'hashtags'])
            ->where('user_id', '!=', $userId)
            ->paginate(5);

        $this->processForumImages($myForums);
        $this->processForumImages($otherForums);

        return Inertia::render('Forums/Index', [
            'myForums' => $myForums,
            'otherForums' => $otherForums->items(),
            'total' => $otherForums->total(),
            'currentPage' => $otherForums->currentPage(),
            'lastPage' => $otherForums->lastPage(),
        ]);
    }

    private function processForumImages($forums)
    {
        foreach ($forums as $forum) {
            $user = $forum->user;
            $forum->user_image = null;

            if ($user->isCoach()) {
                $forum->user_image = $user->coach->profile_image ? Storage::url('images/' . $user->coach->profile_image) : asset('images/placeholder.png');
            } elseif ($user->isStartup()) {
                $forum->user_image = $user->startup->logo_startup ? Storage::url('images/' . $user->startup->logo_startup) : asset('images/placeholder.png');
            } elseif ($user->isInvestisseur()) {
                $forum->user_image = $user->investisseur->profile_image ? Storage::url('images/' . $user->investisseur->profile_image) : asset('images/placeholder.png');
            } elseif ($user->isAdmin()) {
                $forum->user_image = $user->admin->profile_image ? Storage::url($user->admin->profile_image) : asset('images/placeholder.png');
            } else {
                $forum->user_image = asset('images/placeholder.png');
            }
        }
    }

    public function create()
    {
        return Inertia::render('Forums/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:forums',
            'description' => 'required|string',
            'visibility' => 'required|in:public,private',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // validation pour l'image
            'category' => 'required|string',
        ]);

        $forumData = [
            'name' => $request->name,
            'description' => $request->description,
            'visibility' => $request->visibility,
            'category' => $request->category,
            'user_id' => auth()->id(),
        ];

        // Gérer l'upload de l'image
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('forums', 'public');
            $forumData['image'] = $path;
        }

        $forum = Forum::create($forumData);

        // Gestion des hashtags avec vérification des doublons
        if ($request->has('tags')) {
            $tags = collect(json_decode($request->tags))->unique(); // Supprime les doublons dans la requête
            foreach ($tags as $tagName) {
                $hashtag = Hashtag::firstOrCreate(
                    ['name' => $tagName],
                    ['name' => $tagName]
                );
                if (!$forum->hashtags()->where('name', $tagName)->exists()) {
                    $forum->hashtags()->attach($hashtag->id);
                }
            }
        }

        return redirect()->route('forums.index', $forum->id);
    }

    public function show(Forum $forum)
    {   
        // Incrémenter le compteur de vues uniquement si l'utilisateur n'a pas déjà vu le topic
        $sessionKey = 'topic_viewed_' . $forum->id;
        if (!session()->has($sessionKey)) {
            $forum->incrementViewCount();
            session()->put($sessionKey, true);
        }        
        $topics = $forum->topics()
            ->with(['user.coach', 'user.startup', 'user.investisseur'])
            ->paginate(10);

        foreach ($topics as $topic) {
            $user = $topic->user;
            $topic->user_image = $this->getUserImage($user);
        }

        // Correction du chemin de l'image
        if ($forum->image) {
            $forum->image = Storage::url($forum->image);
        }

        return Inertia::render('Forums/Show', [
            'forum' => $forum,
            'topics' => $topics->items(),
            'total' => $topics->total(),
            'currentPage' => $topics->currentPage(),
            'lastPage' => $topics->lastPage(),
        ]);
    }

    private function getUserImage($user)
    {
        if ($user->isCoach()) {
            return $user->coach->profile_image ? Storage::url('images/' . $user->coach->profile_image) : asset('images/placeholder.png');
        } elseif ($user->isStartup()) {
            return $user->startup->logo_startup ? Storage::url('images/' . $user->startup->logo_startup) : asset('images/placeholder.png');
        } elseif ($user->isInvestisseur()) {
            return $user->investisseur->profile_image ? Storage::url('images/' . $user->investisseur->profile_image) : asset('images/placeholder.png');
        } elseif ($user->isAdmin()) {
            return $user->admin->profile_image ? Storage::url($user->admin->profile_image) : asset('images/placeholder.png');
        }
        return asset('images/placeholder.png');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $category = $request->input('category');
        $hashtag = $request->input('hashtag');
        $sortBy = $request->input('sortBy', 'recent');
        $userId = auth()->id();
        
        $forumsQuery = Forum::with(['user.coach', 'user.startup', 'user.investisseur', 'hashtags', 'topics'])
            ->where(function($q) use ($query) {
                if ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                }
            });

        if ($category) {
            $forumsQuery->where('category', $category);
        }

        if ($hashtag) {
            $forumsQuery->whereHas('hashtags', function($q) use ($hashtag) {
                $q->where('name', $hashtag);
            });
        }

        switch ($sortBy) {
            case 'popular':
                $forumsQuery->orderBy('views_count', 'desc');
                break;
            case 'comments':
                $forumsQuery->withCount('topics')->orderBy('topics_count', 'desc');
                break;
            default:
                $forumsQuery->orderBy('created_at', 'desc');
        }

        $forums = $forumsQuery->paginate(10);
        $this->processForumImages($forums);

        // Séparer les forums en deux groupes
        $myForums = $forums->where('user_id', $userId);
        $otherForums = $forums->where('user_id', '!=', $userId);

        return response()->json([
            'myForums' => $myForums->values(),
            'otherForums' => $otherForums->values(),
            'total' => $forums->total(),
            'currentPage' => $forums->currentPage(),
            'lastPage' => $forums->lastPage(),
        ]);
    }

}
