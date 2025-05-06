<template>
    <div class="reports-page">
        <h1 class="page-title">Gestion des signalements</h1>

        <!-- Tabs pour la navigation entre signalements et modération IA -->
        <div class="tabs-container mb-4">
            <div class="tabs-header">
                <button 
                    @click="activeTab = 'reports'" 
                    class="tab-button" 
                    :class="{ 'active': activeTab === 'reports' }"
                >
                    <i class="fas fa-flag mr-2"></i>
                    Signalements utilisateurs
                    <span v-if="unreadReports > 0" class="tab-badge">{{ unreadReports }}</span>
                </button>
                <button 
                    @click="activeTab = 'ai-moderation'" 
                    class="tab-button" 
                    :class="{ 'active': activeTab === 'ai-moderation' }"
                >
                    <i class="fas fa-robot mr-2"></i>
                    Modération IA
                    <span v-if="pendingAIReviews > 0" class="tab-badge">{{ pendingAIReviews }}</span>
                </button>
            </div>
        </div>

        <!-- Tab content - Signalements Utilisateurs -->
        <div v-if="activeTab === 'reports'">
            <!-- Filtres -->
            <div class="filters-container mb-4 p-4 bg-white rounded-lg shadow">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="filter-label">Type</label>
                        <select v-model="filters.type" class="filter-select">
                            <option value="">Tous</option>
                            <option value="App\Models\Post">Post</option>
                            <option value="App\Models\Comment">Commentaire</option>
                        </select>
                    </div>
                    <div>
                        <label class="filter-label">Statut</label>
                        <select v-model="filters.status" class="filter-select">
                            <option value="">Tous</option>
                            <option value="pending">En attente</option>
                            <option value="resolved">Traité</option>
                            <option value="dismissed">Rejeté</option>
                        </select>
                    </div>
                    <div>
                        <label class="filter-label">Raison</label>
                        <select v-model="filters.reason" class="filter-select">
                            <option value="">Toutes</option>
                            <option value="spam">Spam</option>
                            <option value="harassment">Harcèlement</option>
                            <option value="inappropriate">Contenu inapproprié</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>
                    <div>
                        <label class="filter-label">Date</label>
                        <input type="date" v-model="filters.date" class="filter-select">
                    </div>
                </div>
                <!-- Ajout de la barre de recherche -->
                <div class="mt-4">
                    <input
                        type="text"
                        v-model="filters.search"
                        placeholder="Rechercher par description ou nom d'utilisateur..."
                        class="w-full p-2 border border-gray-300 rounded-md focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    />
                </div>
            </div>

            <!-- Loader -->
            <div v-if="loading" class="loader-container">
                <div class="loader"></div>
            </div>

            <!-- Table -->
            <div v-else class="overflow-x-auto bg-white rounded-lg shadow">
                <table v-if="filteredReports.length > 0" class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="table-header">Date</th>
                            <th class="table-header">Motif</th>
                            <th class="table-header">Signalé par</th>
                            <th class="table-header">Statut</th>
                            <th class="table-header">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="report in filteredReports" :key="report.id" class="hover:bg-gray-50">
                            <td class="table-cell">{{ formatDate(report.created_at) }}</td>
                            <td class="table-cell">
                                <div class="font-medium">{{ formatReason(report.reason) }}</div>
                                <div class="text-sm text-gray-500">{{ report.description }}</div>
                            </td>
                            <td class="table-cell">{{ report.user.name }}</td>
                            <td class="table-cell">
                                <span :class="['status-badge', report.status]">
                                    {{ formatStatus(report.status) }}
                                </span>
                            </td>
                            <td class="table-cell">
                                <div class="flex space-x-2">
                                    <button 
                                        @click="viewContent(report)" 
                                        class="action-btn view-btn"
                                        :title="`Voir le contenu (${report.reportable?.user?.name})`">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <template v-if="report.status === 'pending'">
                                        <button @click="resolveReport(report.id)" 
                                            class="action-btn resolve-btn"
                                            title="Marquer comme traité">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button @click="warnUser(report.reportable?.user_id)" 
                                            class="action-btn warn-btn"
                                            title="Envoyer un avertissement à l'utilisateur">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </button>
                                        <button @click="deleteReport(report)" 
                                            class="action-btn delete-btn"
                                            title="Supprimer le contenu signalé">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <!-- Message quand aucun signalement -->
                <div v-else class="p-8 text-center text-gray-500">
                    <i class="fas fa-search mb-4 text-4xl"></i>
                    <p class="text-lg">Aucun signalement trouvé</p>
                </div>
            </div>
        </div>

        <!-- Tab content - Modération IA -->
        <div v-if="activeTab === 'ai-moderation'">
            <AIModerationTab />
        </div>
    </div>
    <ViewReportModal
        :show="viewingReport !== null"
        :report="viewingReport"
        @close="viewingReport = null"
        @resolve="resolveReport(viewingReport?.id)"
        @warn="warnUser(viewingReport?.reportable?.user_id)"
        @delete="deleteReport(viewingReport)"
    />

    <DeleteModal
        :show="showDeleteModal"
        @close="showDeleteModal = false"
        @confirm="confirmDelete"
    />
</template>

<script>
import { ref, onMounted, computed, defineComponent } from 'vue';
import ViewReportModal from './ViewReportModal.vue';
import DeleteModal from './DeleteModal.vue';
import AIModerationTab from './AIModerationTab.vue';

export default defineComponent({
    components: {
        ViewReportModal,
        DeleteModal,
        AIModerationTab
    },
    setup() {
        const reports = ref([]);
        const loading = ref(true);
        const viewingReport = ref(null); // Pour la modal de vue détaillée
        const reportToDelete = ref(null); // Pour la modal de suppression
        const showDeleteModal = ref(false);
        const activeTab = ref('reports');
        const unreadReports = ref(0);
        const pendingAIReviews = ref(0);

        const filters = ref({
            type: '',
            status: '',
            reason: '',
            date: '',
            search: '' // Ajout du filtre de recherche
        });

        const filteredReports = computed(() => {
            return reports.value.filter(report => {
                let matchesType = !filters.value.type || report.reportable_type === filters.value.type;
                let matchesStatus = !filters.value.status || report.status === filters.value.status;
                let matchesReason = !filters.value.reason || report.reason === filters.value.reason;
                let matchesDate = !filters.value.date || report.created_at.startsWith(filters.value.date);
                
                // Ajout de la logique de recherche
                let matchesSearch = true;
                if (filters.value.search) {
                    const searchTerm = filters.value.search.toLowerCase();
                    matchesSearch = (
                        report.description?.toLowerCase().includes(searchTerm) ||
                        report.user?.name?.toLowerCase().includes(searchTerm) ||
                        report.reportable?.user?.name?.toLowerCase().includes(searchTerm)
                    );
                }
                
                return matchesType && matchesStatus && matchesReason && matchesDate && matchesSearch;
            });
        });

        const formatDate = (date) => {
          return new Date(date).toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
          });
        };

        const formatStatus = (status) => {
          const statusMap = {
            pending: 'En attente',
            resolved: 'Traité',
            dismissed: 'Rejeté'
          };
          return statusMap[status] || status;
        };

        const formatReason = (reason) => {
          const reasonMap = {
            spam: 'Spam',
            harassment: 'Harcèlement',
            inappropriate: 'Contenu inapproprié',
            other: 'Autre'
          };
          return reasonMap[reason] || reason;
        };

        const formatContentType = (type) => {
          const typeMap = {
            'App\\Models\\Post': 'réponse',
            'App\\Models\\Comment': 'Commentaire'
          };
          return typeMap[type] || type;
        };

        const loadReports = async () => {
          try {
            loading.value = true;
            const response = await axios.get('/admin/reports');
            reports.value = response.data.reports;
            
            // Compter les signalements non lus
            unreadReports.value = reports.value.filter(r => r.status === 'pending').length;
          } catch (error) {
            console.error('Erreur lors du chargement des signalements:', error);
          } finally {
            loading.value = false;
          }
        };

        const loadPendingAIReviews = async () => {
            try {
                const response = await axios.get('/admin/moderation', {
                    params: { reviewed: 'false' }
                });
                pendingAIReviews.value = response.data.stats.pending_review || 0;
            } catch (error) {
                console.error('Erreur lors du chargement des révisions IA en attente:', error);
            }
        };

        const viewContent = (report) => {
            viewingReport.value = report;
        };

        const resolveReport = async (reportId) => {
            if (!reportId) return;
            try {
                await axios.post(`/admin/reports/${reportId}`, { status: 'resolved' });
                await loadReports();
                viewingReport.value = null;
                showToast({
                    type: 'success',
                    message: 'Signalement marqué comme traité'
                });
            } catch (error) {
                console.error('Erreur lors de la résolution:', error);
                showToast({
                    type: 'error',
                    message: 'Une erreur est survenue lors de la résolution du signalement'
                });
            }
        };

        const warnUser = async (userId) => {
            if (!userId) return;
            
            try {
                const warningResponse = await axios.post('/admin/warnings', {
                    user_id: userId,
                    reason: 'Contenu inapproprié',
                    description: 'Avertissement automatique suite à un signalement'
                });

                if (!warningResponse.data.success) {
                    throw new Error('Échec de la création de l\'avertissement');
                }
                
                const notificationResponse = await axios.post(`/notifications/warning/${userId}`, {
                    reason: 'Contenu inapproprié'
                });

                if (!notificationResponse.data.success) {
                    throw new Error('Échec de l\'envoi de la notification');
                }
                
                if (viewingReport.value) {
                    await resolveReport(viewingReport.value.id);
                }
                
                showDeleteModal.value = false;
                viewingReport.value = null;
                await loadReports();
                showToast({
                    type: 'success',
                    message: 'Avertissement envoyé avec succès'
                });
            } catch (error) {
                console.error('Erreur lors de l\'envoi de l\'avertissement:', error);
                showToast({
                    type: 'error',
                    message: error.response?.data?.message || 'Une erreur est survenue lors de l\'envoi de l\'avertissement'
                });
            }
        };

        const deleteReport = (report) => {
            reportToDelete.value = report;
            showDeleteModal.value = true;
        };

        const confirmDelete = async () => {
            try {
                const report = reportToDelete.value;
                if (!report?.id) return;
                
                const response = await axios.delete(`/admin/reports/${report.id}/content`);
                
                if (response.data.success) {
                    await axios.post(`/notifications/content-deleted/${report.user_id}`, {
                        content_type: report.reportable_type,
                        report_id: report.id
                    });
                    
                    showDeleteModal.value = false;
                    reportToDelete.value = null;
                    await loadReports();
                    showToast({
                        type: 'success',
                        message: 'Contenu supprimé avec succès'
                    });
                } else {
                    throw new Error(response.data.message || 'Erreur lors de la suppression');
                }
            } catch (error) {
                console.error('Erreur lors de la suppression:', error);
                showToast({
                    type: 'error',
                    message: error.response?.data?.message || 'Une erreur est survenue lors de la suppression du contenu'
                });
            }
        };

        const showToast = ({ type, message }) => {
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
        };

        // Initialisation
        onMounted(() => {
            loadReports();
            loadPendingAIReviews();
            
            // Mettre à jour les compteurs toutes les 5 minutes
            const updateCountersInterval = setInterval(() => {
                loadReports();
                loadPendingAIReviews();
            }, 5 * 60 * 1000);
            
            // Nettoyer l'intervalle lors du démontage
            return () => clearInterval(updateCountersInterval);
        });

        return {
            reports,
            loading,
            viewingReport,
            reportToDelete,
            showDeleteModal,
            filters,
            filteredReports,
            formatDate,
            formatStatus,
            formatReason,
            formatContentType,
            viewContent,
            resolveReport,
            warnUser,
            deleteReport,
            confirmDelete,
            showToast,
            activeTab,
            unreadReports,
            pendingAIReviews
        };
    }
});
</script>

<style scoped>
.reports-page {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.page-title {
  font-size: 1.5rem;
  font-weight: bold;
  margin-bottom: 2rem;
  color: #2d3748;
}

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

.overflow-x-auto {
  overflow-x: auto;
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

.warn-btn {
    background-color: #eab308;
}

.warn-btn:hover {
    background-color: #ca8a04;
}

.delete-btn {
    background-color: #dc2626;
}

.delete-btn:hover {
    background-color: #b91c1c;
}

.status-badge {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 500;
    border-radius: 9999px;
}

.status-badge.pending {
    background-color: #fef3c7;
    color: #92400e;
}

.status-badge.resolved {
    background-color: #dcfce7;
    color: #166534;
}

.status-badge.dismissed {
    background-color: #fee2e2;
    color: #991b1b;
}

.filters-container {
    margin-bottom: 1.5rem;
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

.filter-select:focus {
    outline: none;
    border-color: #2563eb;
    ring: 2px solid #2563eb;
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

.tabs-container {
    border-radius: 8px;
    overflow: hidden;
}

.tabs-header {
    display: flex;
    border-bottom: 1px solid #e5e7eb;
    background-color: white;
}

.tab-button {
    padding: 1rem 1.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #6b7280;
    border: none;
    background: white;
    cursor: pointer;
    position: relative;
    display: inline-flex;
    align-items: center;
    transition: all 0.3s ease;
}

.tab-button.active {
    color: #2563eb;
    font-weight: 600;
}

.tab-button.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: #2563eb;
}

.tab-button:hover {
    color: #2563eb;
    background-color: #f9fafb;
}

.tab-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background-color: #ef4444;
    color: white;
    font-size: 0.75rem;
    font-weight: 600;
    margin-left: 8px;
}

.mr-2 {
    margin-right: 0.5rem;
}
</style>
