<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\ModeratedContent;
use Illuminate\Support\Facades\Log;

class ModerationService
{
    protected $apiUrl;
    protected $actionThreshold;
    protected $blockThreshold;
    
    public function __construct()
    {
        $this->apiUrl = env('MODERATOR_API_URL', 'http://localhost:5001/moderate');
        $this->actionThreshold = env('MODERATION_ACTION_THRESHOLD', 0.7);
        $this->blockThreshold = env('MODERATION_BLOCK_THRESHOLD', 0.9);
    }
    
    /**
     * Modérer un contenu et déterminer l'action à prendre
     *
     * @param string $content Le contenu à modérer
     * @param string $lang La langue du contenu (par défaut: fr)
     * @return array Résultat de la modération avec action recommandée
     */
    public function moderateContent(string $content, string $lang = 'fr'): array
    {
        try {
            $response = Http::post($this->apiUrl, [
                'text' => $content,
                'lang' => $lang
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                $action = $this->determineAction($result);
                
                return array_merge($result, ['action' => $action]);
            } else {
                Log::error('Erreur de l\'API de modération: ' . $response->status());
                return [
                    'is_toxic' => false,
                    'error' => 'Erreur de service',
                    'action' => 'none'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception du service de modération: ' . $e->getMessage());
            return [
                'is_toxic' => false,
                'error' => 'Exception: ' . $e->getMessage(),
                'action' => 'none'
            ];
        }
    }
    
    /**
     * Déterminer l'action à prendre en fonction des résultats de modération
     *
     * @param array $result Résultat de la modération
     * @return string Action à prendre ('none', 'flag', 'modify', 'block')
     */
    protected function determineAction(array $result): string
    {
        if (!$result['is_toxic']) {
            return 'none';
        }
        
        // Si le score le plus toxique dépasse le seuil de blocage
        if ($result['most_toxic_score'] > $this->blockThreshold) {
            return 'block';
        }
        
        // Si le score le plus toxique dépasse le seuil d'action
        if ($result['most_toxic_score'] > $this->actionThreshold) {
            // Définir si on modifie ou signale en fonction du type de toxicité
            $modifyCats = ['obscene', 'insult', 'identity_hate'];
            if (in_array($result['most_toxic_category'], $modifyCats)) {
                return 'modify';
            }
            
            return 'flag';
        }
        
        return 'flag';
    }
    
    /**
     * Sanitise le contenu identifié comme toxique
     * 
     * @param string $content Le contenu original à sanitiser
     * @return string Le contenu sanitisé
     */
    public function sanitizeContent(string $content): string
    {
        // Liste des mots ou expressions à remplacer
        $profanityWords = [
            // Injures et insultes courantes
            '/merde/i' => '****',
            '/connard/i' => '*******',
            '/putain/i' => '******',
            '/salope/i' => '******',
            '/enculé/i' => '******',
            '/con[ne]?/i' => '***',
            '/bâtard/i' => '******',
            '/pute/i' => '****',
            '/cul/i' => '***',
            '/bite/i' => '****',
            '/couille/i' => '*******',
            '/foutre/i' => '******',
            '/niqu/i' => '****',
            '/pd/i' => '**',
            '/ftg/i' => '***',
            '/ntm/i' => '***',
            '/tg/i' => '**',
            
            // Termes racistes et discriminatoires
            '/négro/i' => '*****',
            '/negro/i' => '*****',
            '/nègre/i' => '*****',
            '/juif/i' => '****',
            '/arabe/i' => '*****',
            '/bougnoule/i' => '*********',
            '/paki/i' => '****',
            '/chinetoque/i' => '**********',
            '/feuj/i' => '****',
            '/youpin/i' => '******',
            '/bicot/i' => '******',
            '/gouine/i' => '******',
            '/pédé/i' => '****',
            '/travelo/i' => '*******',
            
            // Expressions offensantes en anglais 
            '/fuck/i' => '****',
            '/shit/i' => '****',
            '/bitch/i' => '*****',
            '/asshole/i' => '*******',
            '/cunt/i' => '****',
            '/nigger/i' => '******',
            '/faggot/i' => '******',
            '/retard/i' => '******',
        ];
        
        // Appliquer les remplacements
        $sanitized = preg_replace(
            array_keys($profanityWords), 
            array_values($profanityWords), 
            $content
        );
        
        return $sanitized;
    }
    
    /**
     * Enregistrer un résultat de modération dans la base de données
     *
     * @param string $contentType Type de contenu ('Post', 'Comment')
     * @param int $contentId ID du contenu
     * @param string $originalContent Contenu original
     * @param array $result Résultat de la modération
     * @param int $userId ID de l'utilisateur
     * @return ModeratedContent
     */
    public function saveModeration($contentType, $contentId, $originalContent, $result, $userId): ModeratedContent
    {
        $moderatedContent = null;
        
        if ($result['action'] === 'modify') {
            $moderatedContent = $this->sanitizeContent($originalContent);
        }
        
        return ModeratedContent::create([
            'content_type' => $contentType,
            'content_id' => $contentId,
            'original_content' => $originalContent,
            'moderated_content' => $moderatedContent,
            'is_toxic' => $result['is_toxic'] ?? false,
            'toxicity_scores' => $result['scores'] ?? null,
            'most_toxic_category' => $result['most_toxic_category'] ?? null,
            'most_toxic_score' => $result['most_toxic_score'] ?? null,
            'language' => $result['lang'] ?? 'fr',
            'user_id' => $userId,
            'moderation_action' => $result['action'] ?? 'none',
            'admin_reviewed' => false
        ]);
    }
} 