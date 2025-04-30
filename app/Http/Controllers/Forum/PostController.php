<?php

namespace App\Http\Controllers\Forum;

use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Notifications\NewTopicActivityNotification;
use App\Services\ModerationService;
use Inertia\Inertia;

class PostController extends Controller
{
    protected $moderationService;

    public function __construct(ModerationService $moderationService)
    {
        $this->moderationService = $moderationService;
    }
    
    private function getUserImage($user)
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
        
        // Modérer le contenu avant de l'enregistrer
        $moderationResult = $this->moderationService->moderateContent($request->content);
        
        // Si le contenu est bloqué, retourner une erreur
        if ($moderationResult['action'] === 'block') {
            return response()->json([
                'success' => false,
                'message' => 'Ce contenu ne peut pas être publié car il contient des propos inappropriés.',
                'moderation' => [
                    'is_toxic' => true,
                    'category' => $moderationResult['most_toxic_category']
                ]
            ], 403);
        }
        
        // Déterminer le contenu à enregistrer
        $contentToSave = $request->content;
        if ($moderationResult['action'] === 'modify') {
            // Si le contenu est modifié, on utilise la version sanitisée
            $contentToSave = $this->moderationService->sanitizeContent($request->content);
        }
        
        // Créer le post
        $post = $topic->posts()->create([
            'content' => $contentToSave,
            'user_id' => auth()->id()
        ]);
        
        // Enregistrer le résultat de la modération
        $this->moderationService->saveModeration(
            'Post',
            $post->id,
            $request->content,
            $moderationResult,
            auth()->id()
        );

        // Notifier les abonnés du topic
        $topic->subscribers()
            ->where('user_id', '!=', auth()->id())
            ->each(function ($subscriber) use ($topic, $post) {
                $subscriber->notify(new NewTopicActivityNotification(
                    'new_post',
                    $topic,
                    $post->content,
                    auth()->user()
                ));
            });
        
        $response = [
            'success' => true,
            'post' => $this->preparePostData($post)
        ];
        
        // Ajouter des infos de modération si le contenu a été modifié
        if ($moderationResult['action'] === 'modify') {
            $response['moderation'] = [
                'was_modified' => true,
                'reason' => $moderationResult['most_toxic_category']
            ];
        } elseif ($moderationResult['action'] === 'flag') {
            $response['moderation'] = [
                'was_flagged' => true
            ];
        }

        return response()->json($response);
    } 

    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Vous n\'êtes pas autorisé à modifier ce post.'], 403);
        }

        $request->validate([
            'content' => 'required|string'
        ]);
        
        // Modérer le contenu avant de l'enregistrer
        $moderationResult = $this->moderationService->moderateContent($request->content);
        
        // Si le contenu est bloqué, retourner une erreur
        if ($moderationResult['action'] === 'block') {
            return response()->json([
                'success' => false,
                'message' => 'Cette modification ne peut pas être publiée car elle contient des propos inappropriés.',
                'moderation' => [
                    'is_toxic' => true,
                    'category' => $moderationResult['most_toxic_category']
                ]
            ], 403);
        }
        
        // Déterminer le contenu à enregistrer
        $contentToSave = $request->content;
        if ($moderationResult['action'] === 'modify') {
            // Si le contenu est modifié, on utilise la version sanitisée
            $contentToSave = $this->moderationService->sanitizeContent($request->content);
        }

        $post->update([
            'content' => $contentToSave
        ]);
        
        // Enregistrer le résultat de la modération
        $this->moderationService->saveModeration(
            'Post',
            $post->id,
            $request->content,
            $moderationResult,
            auth()->id()
        );
        
        $response = [
            'success' => true,
            'post' => $this->preparePostData($post)
        ];
        
        // Ajouter des infos de modération si le contenu a été modifié
        if ($moderationResult['action'] === 'modify') {
            $response['moderation'] = [
                'was_modified' => true,
                'reason' => $moderationResult['most_toxic_category']
            ];
        } elseif ($moderationResult['action'] === 'flag') {
            $response['moderation'] = [
                'was_flagged' => true
            ];
        }

        return response()->json($response);
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
        $imageName = $image->hashName();
        $path = $image->storeAs('posts/images', $imageName, 's3');

        try {
            $url = Storage::disk('s3')->temporaryUrl('posts/images/' . $imageName, now()->addMinutes(30));
            return response()->json([
                'success' => true,
                'url' => $url
            ]);
        } catch (\Exception $e) {
            \Log::error('S3 temporary URL error for uploaded image: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Image uploaded but URL generation failed'
            ], 500);
        }
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
