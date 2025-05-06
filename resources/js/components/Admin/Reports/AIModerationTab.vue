<template>
    <div class="ai-moderation-tab">
        <!-- Dashboard Analytics -->
        <div class="analytics-dashboard mb-6 bg-white rounded-lg shadow p-4">
            <h2 class="text-lg font-semibold mb-3">Tableau de bord IA</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="stat-card flex flex-col p-4 border rounded-lg">
                    <span class="text-sm text-gray-500">Total contenus analysés</span>
                    <span class="text-2xl font-bold">{{ stats.global?.total || 0 }}</span>
                </div>
                <div class="stat-card flex flex-col p-4 border rounded-lg bg-red-50">
                    <span class="text-sm text-gray-500">Contenus toxiques</span>
                    <span class="text-2xl font-bold text-red-500">{{ stats.global?.toxic || 0 }}</span>
                </div>
                <div class="stat-card flex flex-col p-4 border rounded-lg bg-blue-50">
                    <span class="text-sm text-gray-500">En attente de révision</span>
                    <span class="text-2xl font-bold text-blue-600">{{ stats.global?.pending || 0 }}</span>
                </div>
                <div class="stat-card flex flex-col p-4 border rounded-lg bg-green-50">
                    <span class="text-sm text-gray-500">Traités par admin</span>
                    <span class="text-2xl font-bold text-green-600">{{ stats.global?.reviewed || 0 }}</span>
                </div>
            </div>
            
            <!-- Graphiques et visualisations -->
            <div class="charts-container mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="chart-card p-4 border rounded-lg">
                    <h3 class="text-md font-semibold mb-2">Distribution par catégorie</h3>
                    <div class="h-64 flex flex-col justify-center items-center">
                        <div v-if="Object.keys(stats.by_category || {}).length" class="w-full h-full">
                            <!-- Remplacer par un vrai graphique (utilisez Chart.js ou similaire) -->
                            <div v-for="(count, category) in stats.by_category" :key="category" class="flex items-center mb-2">
                                <div class="w-24 text-xs truncate" :title="formatCategory(category)">{{ formatCategory(category) }}</div>
                                <div class="flex-grow h-6 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-500" :style="{width: calculatePercentage(count, getTotalByCategory()) + '%'}"></div>
                                </div>
                                <div class="ml-2 text-xs w-10 text-right">{{ count }}</div>
                            </div>
                        </div>
                        <div v-else class="text-gray-400 flex h-full items-center">
                            Aucune donnée disponible
                        </div>
                    </div>
                </div>
                
                <div class="chart-card p-4 border rounded-lg">
                    <h3 class="text-md font-semibold mb-2">Actions de modération</h3>
                    <div class="h-64 flex flex-col justify-center items-center">
                        <div v-if="Object.keys(stats.by_action || {}).length" class="w-full">
                            <!-- Visualisation des actions -->
                            <div v-for="(count, action) in stats.by_action" :key="action" class="flex items-center mb-2">
                                <div class="w-20 text-xs">{{ formatAction(action) }}</div>
                                <div class="flex-grow h-6 bg-gray-200 rounded-full overflow-hidden">
                                    <div :class="getActionColorClass(action)" class="h-full" 
                                         :style="{width: calculatePercentage(count, getTotalByAction()) + '%'}"></div>
                                </div>
                                <div class="ml-2 text-xs w-10 text-right">{{ count }}</div>
                            </div>
                        </div>
                        <div v-else class="text-gray-400 flex h-full items-center">
                            Aucune donnée disponible
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Affichage du niveau de toxicité -->
            <div class="toxicity-levels mt-6">
                <h3 class="text-md font-semibold mb-2">Niveaux de toxicité</h3>
                <div class="flex items-center">
                    <div class="w-full bg-gray-200 rounded-full h-8 overflow-hidden">
                        <div class="flex h-full">
                            <div class="bg-yellow-400 h-full" 
                                 :style="{width: calculatePercentage(stats.by_toxicity_level?.['0.7-0.8'] || 0, getTotalByToxicityLevel()) + '%'}"
                                 title="Toxicité modérée (70-80%)"></div>
                            <div class="bg-orange-500 h-full" 
                                 :style="{width: calculatePercentage(stats.by_toxicity_level?.['0.8-0.9'] || 0, getTotalByToxicityLevel()) + '%'}"
                                 title="Toxicité élevée (80-90%)"></div>
                            <div class="bg-red-600 h-full" 
                                 :style="{width: calculatePercentage(stats.by_toxicity_level?.['0.9-1.0'] || 0, getTotalByToxicityLevel()) + '%'}"
                                 title="Toxicité très élevée (90-100%)"></div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-between text-xs mt-1">
                    <div>
                        <span class="inline-block w-3 h-3 bg-yellow-400 rounded-sm mr-1"></span>
                        <span>70-80%: {{ stats.by_toxicity_level?.['0.7-0.8'] || 0 }}</span>
                    </div>
                    <div>
                        <span class="inline-block w-3 h-3 bg-orange-500 rounded-sm mr-1"></span>
                        <span>80-90%: {{ stats.by_toxicity_level?.['0.8-0.9'] || 0 }}</span>
                    </div>
                    <div>
                        <span class="inline-block w-3 h-3 bg-red-600 rounded-sm mr-1"></span>
                        <span>90-100%: {{ stats.by_toxicity_level?.['0.9-1.0'] || 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filtres pour les contenus modérés -->
        <div class="filters-container mb-4 p-4 bg-white rounded-lg shadow">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="filter-label">Type de contenu</label>
                    <select v-model="filters.content_type" class="filter-select">
                        <option value="">Tous</option>
                        <option value="Post">Post</option>
                        <option value="Comment">Commentaire</option>
                    </select>
                </div>
                <div>
                    <label class="filter-label">Action</label>
                    <select v-model="filters.action" class="filter-select">
                        <option value="">Toutes</option>
                        <option value="none">Aucune action</option>
                        <option value="flag">Signalé</option>
                        <option value="modify">Modifié</option>
                        <option value="block">Bloqué</option>
                    </select>
                </div>
                <div>
                    <label class="filter-label">Catégorie</label>
                    <select v-model="filters.category" class="filter-select">
                        <option value="">Toutes</option>
                        <option value="toxicity">Toxicité générale</option>
                        <option value="severe_toxicity">Toxicité sévère</option>
                        <option value="obscene">Obscénité</option>
                        <option value="threat">Menace</option>
                        <option value="insult">Insulte</option>
                        <option value="identity_hate">Discrimination</option>
                    </select>
                </div>
                <div>
                    <label class="filter-label">Score minimum</label>
                    <select v-model="filters.min_score" class="filter-select">
                        <option value="">Tous</option>
                        <option value="0.7">≥ 70%</option>
                        <option value="0.8">≥ 80%</option>
                        <option value="0.9">≥ 90%</option>
                    </select>
                </div>
                <div>
                    <label class="filter-label">Statut</label>
                    <select v-model="filters.reviewed" class="filter-select">
                        <option value="">Tous</option>
                        <option value="false">En attente de révision</option>
                        <option value="true">Traité par admin</option>
                    </select>
                </div>
            </div>
            <!-- Barre de recherche -->
            <div class="mt-4">
                <input
                    type="text"
                    v-model="filters.search"
                    placeholder="Rechercher dans les contenus..."
                    class="w-full p-2 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                />
            </div>
        </div>
        
        <!-- Tableau des contenus modérés -->
        <div v-if="loading" class="loader-container">
            <div class="loader"></div>
        </div>
        <div v-else class="overflow-x-auto bg-white rounded-lg shadow">
            <table v-if="moderatedContents.length > 0" class="min-w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="table-header">Date</th>
                        <th class="table-header">Contenu</th>
                        <th class="table-header">Utilisateur</th>
                        <th class="table-header">Catégorie</th>
                        <th class="table-header">Score</th>
                        <th class="table-header">Action IA</th>
                        <th class="table-header">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="content in moderatedContents" :key="content.id" :class="{ 'bg-red-50': content.is_toxic && content.most_toxic_score > 0.9 }">
                        <td class="table-cell">{{ formatDate(content.created_at) }}</td>
                        <td class="table-cell">
                            <div class="max-w-xs truncate">{{ truncateText(content.original_content, 50) }}</div>
                        </td>
                        <td class="table-cell">{{ content.user?.name || 'Utilisateur inconnu' }}</td>
                        <td class="table-cell">
                            <span v-if="content.most_toxic_category" class="category-badge" :class="getCategoryClass(content.most_toxic_category)">
                                {{ formatCategory(content.most_toxic_category) }}
                            </span>
                            <span v-else>-</span>
                        </td>
                        <td class="table-cell">
                            <div class="toxicity-meter" v-if="content.most_toxic_score">
                                <div class="meter-bar">
                                    <div class="meter-fill" :style="{width: (content.most_toxic_score * 100) + '%', backgroundColor: getToxicityColor(content.most_toxic_score)}"></div>
                                </div>
                                <span class="meter-value">{{ Math.round(content.most_toxic_score * 100) }}%</span>
                            </div>
                            <span v-else>-</span>
                        </td>
                        <td class="table-cell">
                            <span class="action-badge" :class="getActionClass(content.moderation_action)">
                                {{ formatAction(content.moderation_action) }}
                            </span>
                        </td>
                        <td class="table-cell">
                            <div class="flex space-x-2">
                                <button 
                                    @click="viewContent(content)" 
                                    class="action-btn view-btn"
                                    title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button 
                                    v-if="!content.admin_reviewed"
                                    @click="reviewContent(content)" 
                                    class="action-btn resolve-btn"
                                    title="Examiner le contenu">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button 
                                    @click="replayModeration(content.id)" 
                                    class="action-btn replay-btn"
                                    title="Rejouer la modération">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- Message quand aucun contenu modéré -->
            <div v-else class="p-8 text-center text-gray-500">
                <i class="fas fa-robot mb-4 text-4xl"></i>
                <p class="text-lg">Aucun contenu modéré trouvé</p>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="moderatedContents.length > 0" class="pagination-container mt-4 flex justify-center">
            <!-- Pagination controls -->
        </div>
        
        <!-- Modals for viewing and reviewing content -->
        <ModerationDetailsModal
            :show="viewingContent !== null"
            :content="viewingContent"
            @close="viewingContent = null"
            @review="startReview"
            @replay="replayModeration(viewingContent?.id)"
        />
        
        <ModerationReviewModal
            :show="reviewingContent !== null"
            :content="reviewingContent"
            @close="reviewingContent = null"
            @submit="submitReview"
        />
    </div>
</template>

<script>
import axios from 'axios';
import ModerationDetailsModal from './ModerationDetailsModal.vue';
import ModerationReviewModal from './ModerationReviewModal.vue';

export default {
    components: {
        ModerationDetailsModal,
        ModerationReviewModal
    },
    data() {
        return {
            loading: true,
            moderatedContents: [],
            stats: {
                global: {},
                by_category: {},
                by_action: {},
                by_toxicity_level: {},
                time_evolution: []
            },
            filters: {
                content_type: '',
                action: '',
                category: '',
                min_score: '',
                reviewed: '',
                search: ''
            },
            viewingContent: null,
            reviewingContent: null,
            pagination: {
                current_page: 1,
                per_page: 15,
                total: 0
            }
        };
    },
    mounted() {
        this.fetchStats();
        this.fetchModeratedContents();
    },
    watch: {
        filters: {
            handler() {
                this.fetchModeratedContents();
            },
            deep: true
        }
    },
    methods: {
        async fetchStats() {
            try {
                const response = await axios.get('/admin/moderation/stats');
                this.stats = response.data;
            } catch (error) {
                console.error('Erreur lors du chargement des statistiques:', error);
            }
        },
        
        async fetchModeratedContents() {
            try {
                this.loading = true;
                
                // Construire les paramètres de la requête
                const params = {};
                Object.keys(this.filters).forEach(key => {
                    if (this.filters[key]) {
                        params[key] = this.filters[key];
                    }
                });
                
                params.page = this.pagination.current_page;
                
                const response = await axios.get('/admin/moderation', { params });
                this.moderatedContents = response.data.moderated_contents.data;
                this.pagination = {
                    current_page: response.data.moderated_contents.current_page,
                    per_page: response.data.moderated_contents.per_page,
                    total: response.data.moderated_contents.total
                };
                
                // Mettre à jour les statistiques locales
                this.stats.global.pending = response.data.stats.pending_review;
            } catch (error) {
                console.error('Erreur lors du chargement des contenus modérés:', error);
            } finally {
                this.loading = false;
            }
        },
        
        viewContent(content) {
            this.viewingContent = content;
        },
        
        reviewContent(content) {
            this.reviewingContent = content;
        },
        
        async replayModeration(contentId) {
            if (!contentId) return;
            
            try {
                const response = await axios.post(`/admin/moderation/${contentId}/replay`);
                
                if (response.data.success) {
                    // Si on visualisait ce contenu, mettre à jour
                    if (this.viewingContent && this.viewingContent.id === contentId) {
                        this.viewingContent = response.data.moderated_content;
                    }
                    
                    await this.fetchModeratedContents();
                    this.showToast({
                        type: 'success',
                        message: 'Modération rejouée avec succès'
                    });
                }
            } catch (error) {
                console.error('Erreur lors du rejeu de la modération:', error);
                this.showToast({
                    type: 'error',
                    message: 'Erreur lors du rejeu de la modération'
                });
            }
        },
        
        startReview(content) {
            this.viewingContent = null;
            this.reviewingContent = content;
        },
        
        async submitReview(data) {
            if (!this.reviewingContent) return;
            
            try {
                const response = await axios.put(`/admin/moderation/${this.reviewingContent.id}`, {
                    admin_reviewed: true,
                    moderation_action: data.action,
                    admin_notes: data.notes
                });
                
                if (response.data.success) {
                    this.reviewingContent = null;
                    await this.fetchModeratedContents();
                    await this.fetchStats();
                    
                    this.showToast({
                        type: 'success',
                        message: 'Contenu examiné avec succès'
                    });
                }
            } catch (error) {
                console.error('Erreur lors de la révision:', error);
                this.showToast({
                    type: 'error',
                    message: 'Erreur lors de la révision du contenu'
                });
            }
        },
        
        formatDate(date) {
            return new Date(date).toLocaleString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        
        truncateText(text, length) {
            if (!text) return '';
            return text.length > length ? text.substring(0, length) + '...' : text;
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
                'none': 'Aucune',
                'flag': 'Signalé',
                'modify': 'Modifié',
                'block': 'Bloqué'
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
        
        getActionColorClass(action) {
            const classes = {
                'none': 'bg-gray-400',
                'flag': 'bg-yellow-400',
                'modify': 'bg-blue-500',
                'block': 'bg-red-500'
            };
            
            return classes[action] || 'bg-gray-400';
        },
        
        getToxicityColor(score) {
            if (score >= 0.9) return '#DC2626';  // red-600
            if (score >= 0.8) return '#F59E0B';  // amber-500
            return '#FBBF24';  // amber-400
        },
        
        calculatePercentage(value, total) {
            if (!total) return 0;
            return Math.round((value / total) * 100);
        },
        
        getTotalByCategory() {
            if (!this.stats.by_category) return 0;
            return Object.values(this.stats.by_category).reduce((sum, count) => sum + count, 0);
        },
        
        getTotalByAction() {
            if (!this.stats.by_action) return 0;
            return Object.values(this.stats.by_action).reduce((sum, count) => sum + count, 0);
        },
        
        getTotalByToxicityLevel() {
            if (!this.stats.by_toxicity_level) return 0;
            return Object.values(this.stats.by_toxicity_level).reduce((sum, count) => sum + count, 0);
        },
        
        showToast({ type, message }) {
            const toast = document.createElement('div');
            toast.className = `notification-toast ${type}`;
            toast.innerHTML = `
                <div class="toast-content">
                    <p>${message}</p>
                </div>
            `;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease-out forwards';
                setTimeout(() => toast.remove(), 300);
            }, 4700);
        }
    }
};
</script>

<style scoped>
.loader-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 200px;
}

.loader {
    border: 4px solid #f3f3f3;
    border-radius: 50%;
    border-top: 4px solid #3498db;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.table-header {
    padding: 0.75rem 1.5rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #6b7280;
}

.table-cell {
    padding: 1rem 1.5rem;
    white-space: nowrap;
    font-size: 0.875rem;
}

.category-badge {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 500;
    border-radius: 9999px;
    white-space: nowrap;
}

.action-badge {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 500;
    border-radius: 9999px;
}

.action-btn {
    width: 32px;
    height: 32px;
    padding: 0.5rem;
    border-radius: 0.5rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    position: relative;
    color: white;
}

.view-btn {
    background-color: #2563eb;
}

.view-btn:hover {
    background-color: #1d4ed8;
}

.resolve-btn {
    background-color: #16a34a;
}

.resolve-btn:hover {
    background-color: #15803d;
}

.replay-btn {
    background-color: #8b5cf6;
}

.replay-btn:hover {
    background-color: #7c3aed;
}

.filter-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
}

.filter-select {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    background-color: #fff;
    font-size: 0.875rem;
}

.toxicity-meter {
    display: flex;
    align-items: center;
    gap: 8px;
}

.meter-bar {
    width: 80px;
    height: 8px;
    background-color: #f3f4f6;
    border-radius: 4px;
    overflow: hidden;
}

.meter-fill {
    height: 100%;
    border-radius: 4px;
}

.meter-value {
    font-size: 0.75rem;
    font-weight: 500;
    color: #374151;
}

.notification-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    border-radius: 8px;
    padding: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    animation: slideIn 0.3s ease-out;
    border-left: 4px solid #4A90E2;
    min-width: 300px;
    max-width: 400px;
}

.notification-toast.success {
    border-left-color: #10B981;
}

.notification-toast.error {
    border-left-color: #EF4444;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}
</style> 