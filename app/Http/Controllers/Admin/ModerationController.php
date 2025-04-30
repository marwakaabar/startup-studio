<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModeratedContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Services\ModerationService;

class ModerationController extends Controller
{
    protected $moderationService;

    public function __construct(ModerationService $moderationService)
    {
        $this->moderationService = $moderationService;
    }

    /**
     * Affiche la liste des contenus modérés par l'IA
     */
    public function index(Request $request)
    {
        $query = ModeratedContent::with('user')
            ->select('moderated_contents.*')
            ->orderBy('created_at', 'desc');
            
        // Appliquer les filtres (si présents)
        if ($request->filled('is_toxic')) {
            $query->where('is_toxic', $request->is_toxic == 'true');
        }
        
        if ($request->filled('action')) {
            $query->where('moderation_action', $request->action);
        }
        
        if ($request->filled('category')) {
            $query->where('most_toxic_category', $request->category);
        }
        
        if ($request->filled('min_score')) {
            $query->where('most_toxic_score', '>=', $request->min_score);
        }
        
        if ($request->filled('reviewed')) {
            $query->where('admin_reviewed', $request->reviewed == 'true');
        }
        
        $moderatedContents = $query->paginate(15);
        
        // Récupérer des statistiques pour les filtres
        $stats = [
            'total' => ModeratedContent::count(),
            'toxic' => ModeratedContent::where('is_toxic', true)->count(),
            'pending_review' => ModeratedContent::where('admin_reviewed', false)->count(),
            'actions' => ModeratedContent::select('moderation_action', DB::raw('count(*) as count'))
                ->groupBy('moderation_action')
                ->pluck('count', 'moderation_action'),
            'categories' => ModeratedContent::select('most_toxic_category', DB::raw('count(*) as count'))
                ->whereNotNull('most_toxic_category')
                ->groupBy('most_toxic_category')
                ->pluck('count', 'most_toxic_category')
        ];
        
        return response()->json([
            'moderated_contents' => $moderatedContents,
            'stats' => $stats
        ]);
    }
    
    /**
     * Affiche les détails d'un contenu modéré
     */
    public function show($id)
    {
        $moderatedContent = ModeratedContent::with('user')->findOrFail($id);
        
        // Récupérer le contenu original (Post ou Comment)
        $content = null;
        
        if ($moderatedContent->content_type === 'Post') {
            $content = \App\Models\Post::with('user', 'topic')->find($moderatedContent->content_id);
        } elseif ($moderatedContent->content_type === 'Comment') {
            $content = \App\Models\Comment::with('user', 'post')->find($moderatedContent->content_id);
        }
        
        return response()->json([
            'moderated_content' => $moderatedContent,
            'content' => $content
        ]);
    }
    
    /**
     * Met à jour le statut d'examen administratif du contenu modéré
     */
    public function update(Request $request, $id)
    {
        $moderatedContent = ModeratedContent::findOrFail($id);
        
        $request->validate([
            'admin_reviewed' => 'boolean',
            'moderation_action' => 'nullable|in:none,flag,modify,block',
            'admin_notes' => 'nullable|string|max:500'
        ]);
        
        $moderatedContent->update([
            'admin_reviewed' => $request->admin_reviewed,
            'moderation_action' => $request->moderation_action ?? $moderatedContent->moderation_action,
            'admin_notes' => $request->admin_notes
        ]);
        
        // Si un changement d'action a été demandé, appliquer les modifications au contenu
        if ($request->filled('moderation_action') && 
            $request->moderation_action !== $moderatedContent->moderation_action) {
            
            $this->applyModificationToContent($moderatedContent, $request->moderation_action);
        }
        
        return response()->json([
            'success' => true,
            'moderated_content' => $moderatedContent
        ]);
    }
    
    /**
     * Renvoie des statistiques globales sur la modération
     */
    public function stats()
    {
        // Statistiques globales
        $globalStats = [
            'total' => ModeratedContent::count(),
            'toxic' => ModeratedContent::where('is_toxic', true)->count(),
            'reviewed' => ModeratedContent::where('admin_reviewed', true)->count(),
        ];
        
        // Distribution par catégorie de toxicité
        $categoryStats = ModeratedContent::select('most_toxic_category', DB::raw('count(*) as count'))
            ->whereNotNull('most_toxic_category')
            ->groupBy('most_toxic_category')
            ->pluck('count', 'most_toxic_category');
        
        // Distribution par action de modération
        $actionStats = ModeratedContent::select('moderation_action', DB::raw('count(*) as count'))
            ->groupBy('moderation_action')
            ->pluck('count', 'moderation_action');
        
        // Distribution par niveau de toxicité
        $toxicityLevels = [
            '0.7-0.8' => ModeratedContent::whereBetween('most_toxic_score', [0.7, 0.8])->count(),
            '0.8-0.9' => ModeratedContent::whereBetween('most_toxic_score', [0.8, 0.9])->count(),
            '0.9-1.0' => ModeratedContent::whereBetween('most_toxic_score', [0.9, 1.0])->count(),
        ];
        
        // Evolution dans le temps (30 derniers jours)
        $timeStats = ModeratedContent::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total'),
                DB::raw('sum(case when is_toxic = 1 then 1 else 0 end) as toxic')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->get();
        
        return response()->json([
            'global' => $globalStats,
            'by_category' => $categoryStats,
            'by_action' => $actionStats,
            'by_toxicity_level' => $toxicityLevels,
            'time_evolution' => $timeStats
        ]);
    }
    
    /**
     * Rejoue la modération sur un contenu existant
     */
    public function replay(Request $request, $id)
    {
        $moderatedContent = ModeratedContent::findOrFail($id);
        
        // Effectuer une nouvelle analyse
        $moderationResult = $this->moderationService->moderateContent(
            $moderatedContent->original_content,
            $moderatedContent->language ?? 'fr'
        );
        
        // Mettre à jour l'enregistrement de modération
        $moderatedContent->update([
            'is_toxic' => $moderationResult['is_toxic'],
            'toxicity_scores' => $moderationResult['scores'] ?? null,
            'most_toxic_category' => $moderationResult['most_toxic_category'] ?? null,
            'most_toxic_score' => $moderationResult['most_toxic_score'] ?? null,
            'moderation_action' => $moderationResult['action'],
            'admin_reviewed' => false, // Réinitialiser, car une nouvelle analyse a été effectuée
        ]);
        
        return response()->json([
            'success' => true,
            'moderated_content' => $moderatedContent,
            'previous_action' => $moderatedContent->moderation_action,
            'new_action' => $moderationResult['action']
        ]);
    }
    
    /**
     * Applique les modifications au contenu original en fonction de l'action de modération
     */
    private function applyModificationToContent($moderatedContent, $newAction)
    {
        // Obtenir le modèle de contenu (Post ou Comment)
        $contentModel = null;
        if ($moderatedContent->content_type === 'Post') {
            $contentModel = \App\Models\Post::find($moderatedContent->content_id);
        } elseif ($moderatedContent->content_type === 'Comment') {
            $contentModel = \App\Models\Comment::find($moderatedContent->content_id);
        }
        
        if (!$contentModel) {
            return; // Le contenu original n'existe plus
        }
        
        switch ($newAction) {
            case 'none':
                // Restaurer le contenu original
                $contentModel->update(['content' => $moderatedContent->original_content]);
                break;
                
            case 'modify':
                // Appliquer la version modérée par l'IA
                $sanitizedContent = $this->moderationService->sanitizeContent($moderatedContent->original_content);
                $contentModel->update(['content' => $sanitizedContent]);
                $moderatedContent->update(['moderated_content' => $sanitizedContent]);
                break;
                
            case 'block':
                // Supprimer le contenu (ou le remplacer par un message)
                $contentModel->update(['content' => '[Ce contenu a été supprimé par un modérateur]']);
                break;
        }
    }
} 