<?php

namespace App\Http\Controllers\Forum;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\NewTopicActivityNotification;
use App\Services\ModerationService;

class CommentController extends Controller
{
    protected $moderationService;

    public function __construct(ModerationService $moderationService)
    {
        $this->moderationService = $moderationService;
    }

    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string',
        ]);
        
        // Modérer le contenu avant de l'enregistrer
        $moderationResult = $this->moderationService->moderateContent($request->content);
        
        // Si le contenu est bloqué, retourner une erreur
        if ($moderationResult['action'] === 'block') {
            return response()->json([
                'success' => false,
                'message' => 'Ce commentaire ne peut pas être publié car il contient des propos inappropriés.',
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

        $comment = $post->comments()->create([
            'content' => $contentToSave,
            'user_id' => auth()->id(),
        ]);
        
        // Enregistrer le résultat de la modération
        $this->moderationService->saveModeration(
            'Comment',
            $comment->id,
            $request->content,
            $moderationResult,
            auth()->id()
        );

        // Notifier les abonnés du topic et l'auteur du post
        $notifyUsers = $post->topic->subscribers()
            ->where('user_id', '!=', auth()->id())
            ->get()
            ->merge([$post->user]);

        $notifyUsers->unique('id')->each(function ($user) use ($post, $comment) {
            $user->notify(new NewTopicActivityNotification(
                'new_comment',
                $post->topic,
                $comment->content,
                auth()->user()
            ));
        });

        // Charger l'utilisateur et ses relations
        $comment->load(['user']);
        
        // Ajouter l'image de profil
        $comment->user_image = $this->getUserProfileImage($comment->user);
        
        $response = [
            'success' => true,
            'comment' => $comment
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

    public function update(Request $request, Comment $comment)
    {
        if ($comment->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Vous n\'êtes pas autorisé à modifier ce commentaire.'], 403);
        }

        $request->validate([
            'content' => 'required|string',
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

        $comment->update([
            'content' => $contentToSave,
        ]);
        
        // Enregistrer le résultat de la modération
        $this->moderationService->saveModeration(
            'Comment',
            $comment->id,
            $request->content,
            $moderationResult,
            auth()->id()
        );

        // Recharger le commentaire avec l'utilisateur
        $comment->load(['user']);
        $comment->user_image = $this->getUserProfileImage($comment->user);

        $response = [
            'success' => true,
            'comment' => $comment,
            'message' => 'Commentaire modifié avec succès'
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

    public function destroy(Comment $comment)
    {
        if (!$comment || ($comment->user_id !== auth()->id() && !auth()->user()->isAdmin())) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à supprimer ce commentaire.'
            ], 403);
        }

        try {
            \DB::beginTransaction();
            
            // Supprimer le commentaire
            $comment->delete();
            
            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Commentaire supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error deleting comment:', [
                'comment_id' => $comment->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du commentaire'
            ], 500);
        }
    }

    public function index(Post $post)
    {
        $comments = $post->comments()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($comment) {
                $comment->user_image = $this->getUserProfileImage($comment->user);
                return $comment;
            });

        return response()->json($comments);
    }

    /**
     * Get user profile image with S3 temporary URL
     * 
     * @param \App\Models\User $user
     * @return string
     */
    private function getUserProfileImage($user)
    {
        $image = null;
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
