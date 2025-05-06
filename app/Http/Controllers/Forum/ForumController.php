<?php

namespace App\Http\Controllers\Forum;

use App\Models\ForumParticipation;
use Illuminate\Support\Facades\Notification;
use App\Models\Forum;
use App\Models\Hashtag;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Notifications\ParticipationRequestNotification;
use App\Notifications\ParticipationResponseNotification;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $query = $request->input('query');
        $category = $request->input('category');
        $sortBy = $request->input('sortBy', 'recent');
        $perPage = 6;

        $baseQuery = Forum::with(['user', 'hashtags', 'topics']);

        if ($query) {
            $baseQuery->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            });
        }

        if ($category) {
            $baseQuery->where('category', $category);
        }

        switch ($sortBy) {
            case 'popular':
                $baseQuery->orderBy('views_count', 'desc');
                break;
            case 'comments':
                $baseQuery->withCount('topics')->orderBy('topics_count', 'desc');
                break;
            default:
                $baseQuery->orderBy('created_at', 'desc');
        }

        $myForums = clone $baseQuery;
        $otherForums = clone $baseQuery;

        $myForums = $myForums->where('user_id', $userId)->get();
        $otherForums = $otherForums->where('user_id', '!=', $userId)->get();

        // Process images for both collections
        $myForums = $this->processForumImages($myForums);
        $otherForums = $this->processForumImages($otherForums);

        if ($request->wantsJson()) {
            return response()->json([
                'myForums' => $myForums,
                'otherForums' => $otherForums
            ]);
        }

        return view('startup.forum.index', compact('myForums', 'otherForums'));
    }

    private function processForumImages($forums)
    {
        return $forums->map(function($forum) {
            if ($forum->image) {
                try {
                    $forum->image = Storage::disk('s3')->temporaryUrl($forum->image, now()->addMinutes(30));
                } catch (\Exception $e) {
                    $forum->image = null;
                }
            }

            if (!$forum->image) {
                $forum->image = "https://eu.ui-avatars.com/api/?background=0093ee&color=fff&bold=true&name=" . urlencode($forum->name);
            }

            return $forum;
        });
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

    public function create()
    {
        return view('startup.forum.add');
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
            $image = $request->file('image');
            $imageName = $image->hashName();
            $image->storeAs('forums', $imageName, 's3');
            $forumData['image'] = 'forums/' . $imageName;
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
        $topics = $forum->topics()
            ->with(['user', 'posts', 'hashtags'])
            ->latest()
            ->get();

        if (request()->wantsJson()) {
            return response()->json([
                'forum' => $forum,
                'topics' => $topics
            ]);
        }

        return view('startup.Forum.show', [
            'forum' => $forum,
            'topics' => $topics
        ]);
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


    public function requestParticipation(Forum $forum)
    {
        try {
            $user = auth()->user();

            // Check if user is not the forum creator
            if ($forum->user_id === $user->id) {
                return response()->json(['message' => 'Vous ne pouvez pas demander à participer à votre propre forum'], 400);
            }

            // Check if request already exists
            $existingRequest = ForumParticipation::where('forum_id', $forum->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingRequest) {
                return response()->json(['message' => 'Demande déjà envoyée'], 400);
            }

            // Create participation request
            $participation = ForumParticipation::create([
                'forum_id' => $forum->id,
                'user_id' => $user->id,
                'status' => 'pending'
            ]);

            // Send notification to forum owner
            $forum->user->notify(new ParticipationRequestNotification($forum, $user));

            return response()->json(['message' => 'Demande envoyée avec succès', 'status' => 'pending']);
            
        } catch (\Exception $e) {
            \Log::error('Forum participation request error: ' . $e->getMessage());
            return response()->json(['message' => 'Une erreur est survenue'], 500);
        }
    }
  
    /**
     * Respond to a participation request.
     *
     * @param Request $request
     * @param Forum $forum
     * @return \Illuminate\Http\JsonResponse
     */

    public function respondToParticipation(Request $request, Forum $forum)
    {
        try {
            $request->validate([
                'status' => 'required|in:accepted,rejected'
            ]);

            // Trouver la participation sans utiliser user_id de la requête
            $participation = ForumParticipation::where('forum_id', $forum->id)
                ->where('status', 'pending')
                ->latest()
                ->firstOrFail();

            $participation->update(['status' => $request->status]);

            // Envoyer une notification à l'utilisateur
            $participation->user->notify(new ParticipationResponseNotification($participation));

            return response()->json([
                'message' => 'Réponse envoyée avec succès',
                'status' => $request->status
            ]);
        } catch (\Exception $e) {
            \Log::error('Forum participation response error: ' . $e->getMessage());
            return response()->json(['message' => 'Une erreur est survenue'], 500);
        }
    }

    public function getParticipationStatus(Forum $forum)
    {
        $participation = ForumParticipation::where('forum_id', $forum->id)
            ->where('user_id', auth()->id())
            ->first();

        return response()->json(['status' => $participation ? $participation->status : null]);
    }


}
