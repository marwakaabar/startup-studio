<template>
    <div v-if="show" class="modal-backdrop">
        <div class="modal-container" @click.stop>
            <div class="modal-header">
                <h3 class="modal-title">Examiner le contenu</h3>
                <button class="modal-close" @click="$emit('close')">×</button>
            </div>
            
            <div class="modal-body">
                <div v-if="content" class="review-form">
                    <div class="ai-decision">
                        <h4>Décision de l'IA</h4>
                        <div class="decision-box">
                            <div class="decision-item">
                                <span class="decision-label">Catégorie:</span>
                                <span class="category-badge" :class="getCategoryClass(content.most_toxic_category)">
                                    {{ formatCategory(content.most_toxic_category) }}
                                </span>
                            </div>
                            <div class="decision-item">
                                <span class="decision-label">Score de toxicité:</span>
                                <div class="toxicity-meter">
                                    <div class="meter-track">
                                        <div class="meter-bar" :style="{width: (content.most_toxic_score * 100) + '%', backgroundColor: getScoreColor(content.most_toxic_score)}"></div>
                                    </div>
                                    <div class="meter-value">{{ Math.round(content.most_toxic_score * 100) }}%</div>
                                </div>
                            </div>
                            <div class="decision-item">
                                <span class="decision-label">Action recommandée:</span>
                                <span class="action-badge" :class="getActionClass(content.moderation_action)">
                                    {{ formatAction(content.moderation_action) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="content-preview">
                        <h4>Contenu</h4>
                        <div class="content-box">{{ content.original_content }}</div>
                        
                        <div v-if="content.moderated_content" class="modified-content">
                            <h5>Version modifiée par l'IA</h5>
                            <div class="content-box modified">{{ content.moderated_content }}</div>
                        </div>
                    </div>
                    
                    <div class="admin-decision">
                        <h4>Votre décision</h4>
                        
                        <div class="decision-options">
                            <div class="option-group">
                                <label class="block mb-2">Action à prendre</label>
                                <div class="action-options">
                                    <label class="action-option" :class="{ 'selected': reviewDecision.action === 'none' }">
                                        <input type="radio" v-model="reviewDecision.action" value="none" class="hidden">
                                        <div class="option-content">
                                            <i class="fas fa-check-circle text-green-500"></i>
                                            <div class="option-text">
                                                <div class="option-title">Approuver</div>
                                                <div class="option-desc">Le contenu est acceptable</div>
                                            </div>
                                        </div>
                                    </label>
                                    
                                    <label class="action-option" :class="{ 'selected': reviewDecision.action === 'flag' }">
                                        <input type="radio" v-model="reviewDecision.action" value="flag" class="hidden">
                                        <div class="option-content">
                                            <i class="fas fa-flag text-yellow-500"></i>
                                            <div class="option-text">
                                                <div class="option-title">Signaler</div>
                                                <div class="option-desc">Surveiller ce contenu</div>
                                            </div>
                                        </div>
                                    </label>
                                    
                                    <label class="action-option" :class="{ 'selected': reviewDecision.action === 'modify' }">
                                        <input type="radio" v-model="reviewDecision.action" value="modify" class="hidden">
                                        <div class="option-content">
                                            <i class="fas fa-edit text-blue-500"></i>
                                            <div class="option-text">
                                                <div class="option-title">Modifier</div>
                                                <div class="option-desc">Appliquer une version censurée</div>
                                            </div>
                                        </div>
                                    </label>
                                    
                                    <label class="action-option" :class="{ 'selected': reviewDecision.action === 'block' }">
                                        <input type="radio" v-model="reviewDecision.action" value="block" class="hidden">
                                        <div class="option-content">
                                            <i class="fas fa-ban text-red-500"></i>
                                            <div class="option-text">
                                                <div class="option-title">Bloquer</div>
                                                <div class="option-desc">Contenu inapproprié à supprimer</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="notes-group mt-6">
                                <label for="admin-notes" class="block mb-2">Notes (optionnel)</label>
                                <textarea 
                                    id="admin-notes"
                                    v-model="reviewDecision.notes" 
                                    class="admin-notes-input"
                                    placeholder="Ajoutez des notes ou explications concernant votre décision..."
                                    rows="3"></textarea>
                            </div>
                            
                            <div class="sanctions-group mt-4" v-if="reviewDecision.action === 'block'">
                                <div class="flex items-center">
                                    <input type="checkbox" id="send-warning" v-model="reviewDecision.sendWarning" class="mr-2">
                                    <label for="send-warning">Envoyer un avertissement à l'utilisateur</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div v-else class="loading-container">
                    <div class="loader"></div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button class="btn-secondary" @click="$emit('close')">Annuler</button>
                <button class="btn-primary" @click="submitReview">
                    <i class="fas fa-check mr-1"></i> Confirmer la décision
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
    emits: ['close', 'submit'],
    data() {
        return {
            reviewDecision: {
                action: '',
                notes: '',
                sendWarning: false
            }
        };
    },
    watch: {
        content: {
            handler(newContent) {
                if (newContent) {
                    // Préremplir avec l'action recommandée par l'IA
                    this.reviewDecision.action = newContent.moderation_action;
                    this.reviewDecision.notes = '';
                    this.reviewDecision.sendWarning = false;
                }
            },
            immediate: true
        }
    },
    methods: {
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
        },
        
        submitReview() {
            if (!this.reviewDecision.action) {
                alert('Veuillez sélectionner une action');
                return;
            }
            
            this.$emit('submit', {
                action: this.reviewDecision.action,
                notes: this.reviewDecision.notes,
                sendWarning: this.reviewDecision.sendWarning
            });
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

.ai-decision, .content-preview, .admin-decision {
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

.decision-box {
    background-color: #f9fafb;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.decision-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
}

.decision-item:last-child {
    margin-bottom: 0;
}

.decision-label {
    width: 140px;
    font-size: 0.875rem;
    color: #6b7280;
}

.category-badge, .action-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-size: 0.875rem;
    font-weight: 500;
}

.toxicity-meter {
    display: flex;
    align-items: center;
    flex: 1;
    gap: 0.75rem;
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
    font-size: 0.875rem;
    color: #374151;
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

.content-box.modified {
    background-color: #f0f9ff;
    border-color: #bae6fd;
}

.modified-content {
    margin-top: 1rem;
}

.action-options {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

.action-option {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.action-option:hover {
    border-color: #d1d5db;
    background-color: #f9fafb;
}

.action-option.selected {
    border-color: #2563eb;
    background-color: #eff6ff;
}

.option-content {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.option-text {
    display: flex;
    flex-direction: column;
}

.option-title {
    font-weight: 600;
    color: #111827;
    font-size: 0.875rem;
}

.option-desc {
    color: #6b7280;
    font-size: 0.75rem;
}

.admin-notes-input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 0.875rem;
    color: #1f2937;
    resize: vertical;
}

.admin-notes-input:focus {
    outline: none;
    border-color: #2563eb;
    ring: 1px solid #2563eb;
}

.btn-primary, .btn-secondary {
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

.block {
    display: block;
}

.mb-2 {
    margin-bottom: 0.5rem;
}

.mt-4 {
    margin-top: 1rem;
}

.mt-6 {
    margin-top: 1.5rem;
}

.hidden {
    display: none;
}

.mr-2 {
    margin-right: 0.5rem;
}

.flex {
    display: flex;
}

.items-center {
    align-items: center;
}
</style> 