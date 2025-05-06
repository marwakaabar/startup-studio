<template>
    <div v-if="show" class="modal-backdrop">
        <div class="modal-container" @click.stop>
            <div class="modal-header">
                <h3 class="modal-title">Détails de modération</h3>
                <button class="modal-close" @click="$emit('close')">×</button>
            </div>
            
            <div class="modal-body">
                <div v-if="content" class="moderation-details">
                    <!-- Informations générales -->
                    <div class="info-section">
                        <h4>Informations générales</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Type de contenu</span>
                                <span class="info-value">{{ formatContentType(content.content_type) }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Date d'analyse</span>
                                <span class="info-value">{{ formatDate(content.created_at) }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Statut</span>
                                <span class="info-value">
                                    <span class="status-badge" :class="content.admin_reviewed ? 'reviewed' : 'pending'">
                                        {{ content.admin_reviewed ? 'Examiné par admin' : 'En attente de révision' }}
                                    </span>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Auteur</span>
                                <span class="info-value">{{ content.user?.name || 'Inconnu' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Résultats de l'analyse IA -->
                    <div class="analysis-section">
                        <h4>Analyse de toxicité</h4>
                        <div class="toxicity-results">
                            <div class="toxicity-header">
                                <span class="toxicity-badge" :class="getToxicityClass(content.most_toxic_score)">
                                    {{ formatToxicityLevel(content.most_toxic_score) }}
                                </span>
                                <div class="toxicity-action">
                                    <span class="action-label">Action recommandée:</span>
                                    <span class="action-badge" :class="getActionClass(content.moderation_action)">
                                        {{ formatAction(content.moderation_action) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div v-if="content.toxicity_scores" class="scores-breakdown">
                                <h5>Scores par catégorie</h5>
                                <div class="scores-grid">
                                    <div v-for="(score, category) in content.toxicity_scores" :key="category" class="score-item">
                                        <div class="score-label">{{ formatCategory(category) }}</div>
                                        <div class="score-meter">
                                            <div class="meter-track">
                                                <div class="meter-bar" :style="{width: (score * 100) + '%', backgroundColor: getScoreColor(score)}"></div>
                                            </div>
                                            <div class="meter-value">{{ Math.round(score * 100) }}%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="most-toxic-category" v-if="content.most_toxic_category">
                                <div class="category-header">Catégorie la plus problématique</div>
                                <div class="category-value">
                                    <span class="category-badge" :class="getCategoryClass(content.most_toxic_category)">
                                        {{ formatCategory(content.most_toxic_category) }}
                                    </span>
                                    <span class="category-score">({{ Math.round(content.most_toxic_score * 100) }}%)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contenu original et modifié -->
                    <div class="content-section">
                        <h4>Contenu</h4>
                        <div class="content-comparison">
                            <div class="content-original">
                                <h5>Contenu original</h5>
                                <div class="content-box">{{ content.original_content }}</div>
                            </div>
                            <div class="content-modified" v-if="content.moderated_content">
                                <h5>Contenu modifié par l'IA</h5>
                                <div class="content-box">{{ content.moderated_content }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notes d'administration (si examiné) -->
                    <div class="admin-notes" v-if="content.admin_notes">
                        <h4>Notes d'administration</h4>
                        <div class="notes-box">{{ content.admin_notes }}</div>
                    </div>
                </div>
                <div v-else class="loading-container">
                    <div class="loader"></div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button class="btn-secondary" @click="$emit('close')">Fermer</button>
                <button class="btn-primary" @click="$emit('replay', content)" title="Analyser à nouveau le contenu">
                    <i class="fas fa-sync-alt mr-1"></i> Rejouer l'analyse
                </button>
                <button v-if="!content?.admin_reviewed" class="btn-success" @click="$emit('review', content)">
                    <i class="fas fa-check mr-1"></i> Examiner
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        show: Boolean,
        content: Object
    },
    emits: ['close', 'review', 'replay'],
    methods: {
        formatDate(date) {
            return new Date(date).toLocaleString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        
        formatContentType(type) {
            const types = {
                'Post': 'Réponse sur le forum',
                'Comment': 'Commentaire'
            };
            return types[type] || type;
        },
        
        formatCategory(category) {
            const categories = {
                'toxicity': 'Toxicité',
                'severe_toxicity': 'Toxicité sévère',
                'obscene': 'Obscénité',
                'threat': 'Menace',
                'insult': 'Insulte',
                'identity_hate': 'Discrimination'
            };
            
            return categories[category] || category;
        },
        
        formatAction(action) {
            const actions = {
                'none': 'Aucune action',
                'flag': 'Signaler',
                'modify': 'Modifier',
                'block': 'Bloquer'
            };
            
            return actions[action] || action;
        },
        
        formatToxicityLevel(score) {
            if (!score) return 'Non toxique';
            if (score >= 0.9) return 'Toxicité très élevée';
            if (score >= 0.8) return 'Toxicité élevée';
            if (score >= 0.7) return 'Toxicité modérée';
            return 'Peu toxique';
        },
        
        getToxicityClass(score) {
            if (!score) return 'non-toxic';
            if (score >= 0.9) return 'very-high';
            if (score >= 0.8) return 'high';
            if (score >= 0.7) return 'moderate';
            return 'low';
        },
        
        getCategoryClass(category) {
            const classes = {
                'toxicity': 'bg-yellow-100 text-yellow-800',
                'severe_toxicity': 'bg-red-100 text-red-800',
                'obscene': 'bg-purple-100 text-purple-800',
                'threat': 'bg-red-100 text-red-800',
                'insult': 'bg-orange-100 text-orange-800',
                'identity_hate': 'bg-pink-100 text-pink-800'
            };
            
            return classes[category] || 'bg-gray-100 text-gray-800';
        },
        
        getActionClass(action) {
            const classes = {
                'none': 'bg-gray-100 text-gray-800',
                'flag': 'bg-yellow-100 text-yellow-800',
                'modify': 'bg-blue-100 text-blue-800',
                'block': 'bg-red-100 text-red-800'
            };
            
            return classes[action] || 'bg-gray-100 text-gray-800';
        },
        
        getScoreColor(score) {
            if (score >= 0.9) return '#DC2626';  // red-600
            if (score >= 0.8) return '#F59E0B';  // amber-500
            if (score >= 0.7) return '#FBBF24';  // amber-400
            return '#10B981';  // green-500
        }
    }
};
</script>

<style scoped>
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 50;
}

.modal-container {
    background-color: white;
    border-radius: 8px;
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.modal-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #111827;
}

.modal-close {
    font-size: 1.5rem;
    background: none;
    border: none;
    cursor: pointer;
    color: #6b7280;
}

.modal-body {
    padding: 1.5rem;
    overflow-y: auto;
    flex-grow: 1;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    border-top: 1px solid #e5e7eb;
}

.info-section, .analysis-section, .content-section, .admin-notes {
    margin-bottom: 2rem;
}

h4 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 0.5rem;
}

h5 {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-label {
    font-size: 0.75rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.info-value {
    font-size: 0.875rem;
    color: #1f2937;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge.pending {
    background-color: #fef3c7;
    color: #92400e;
}

.status-badge.reviewed {
    background-color: #dcfce7;
    color: #166534;
}

.toxicity-results {
    background-color: #f9fafb;
    padding: 1rem;
    border-radius: 8px;
}

.toxicity-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.toxicity-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 600;
}

.toxicity-badge.non-toxic {
    background-color: #dcfce7;
    color: #166534;
}

.toxicity-badge.low {
    background-color: #d1fae5;
    color: #065f46;
}

.toxicity-badge.moderate {
    background-color: #fef3c7;
    color: #92400e;
}

.toxicity-badge.high {
    background-color: #fee2e2;
    color: #991b1b;
}

.toxicity-badge.very-high {
    background-color: #fee2e2;
    color: #7f1d1d;
    font-weight: 700;
}

.toxicity-action {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.action-label {
    font-size: 0.75rem;
    color: #6b7280;
}

.action-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.scores-breakdown {
    margin-top: 1rem;
}

.scores-grid {
    display: grid;
    grid-template-columns: repeat(1, 1fr);
    gap: 0.75rem;
}

.score-item {
    display: flex;
    align-items: center;
}

.score-label {
    width: 100px;
    font-size: 0.75rem;
    color: #374151;
}

.score-meter {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.meter-track {
    flex: 1;
    height: 8px;
    background-color: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
}

.meter-bar {
    height: 100%;
}

.meter-value {
    width: 40px;
    font-size: 0.75rem;
    color: #374151;
    text-align: right;
}

.most-toxic-category {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px dashed #e5e7eb;
}

.category-header {
    font-size: 0.875rem;
    color: #374151;
    margin-bottom: 0.5rem;
}

.category-value {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.category-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-size: 0.875rem;
    font-weight: 500;
}

.category-score {
    font-size: 0.875rem;
    color: #6b7280;
}

.content-comparison {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}

@media (min-width: 768px) {
    .content-comparison {
        grid-template-columns: repeat(2, 1fr);
    }
}

.content-box {
    padding: 1rem;
    background-color: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 0.875rem;
    color: #1f2937;
    white-space: pre-wrap;
    max-height: 200px;
    overflow-y: auto;
}

.notes-box {
    padding: 1rem;
    background-color: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 0.875rem;
    color: #1f2937;
    white-space: pre-wrap;
}

.btn-primary, .btn-secondary, .btn-success {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-primary {
    background-color: #2563eb;
    color: white;
}

.btn-primary:hover {
    background-color: #1d4ed8;
}

.btn-secondary {
    background-color: #f3f4f6;
    color: #374151;
}

.btn-secondary:hover {
    background-color: #e5e7eb;
}

.btn-success {
    background-color: #16a34a;
    color: white;
}

.btn-success:hover {
    background-color: #15803d;
}

.loading-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 200px;
}

.loader {
    border: 4px solid #f3f3f3;
    border-radius: 50%;
    border-top: 4px solid #3498db;
    width: 30px;
    height: 30px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.mr-1 {
    margin-right: 0.25rem;
}
</style> 