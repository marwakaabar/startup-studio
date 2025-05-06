<template>
    <Modal :show="show" @close="$emit('close')">
        <div class="report-details-modal">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Détails du signalement</h2>
                <button @click="$emit('close')" class="close-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="grid gap-4">
                <div class="info-group">
                    <h3 class="text-lg font-semibold text-gray-700">Contenu signalé</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="mb-2"><strong>Type:</strong> {{ formatContentType(report.reportable_type) }}</p>
                        <p class="mb-2"><strong>Auteur:</strong> {{ report.reportable?.user?.name }}</p>
                        <div class="border-l-4 border-gray-300 pl-4 mt-2">
                            <p class="text-gray-700">{{ report.reportable?.content }}</p>
                        </div>
                    </div>
                </div>

                <div class="info-group">
                    <h3 class="text-lg font-semibold text-gray-700">Informations du signalement</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="mb-2"><strong>Signalé par:</strong> {{ report.user.name }}</p>
                        <p class="mb-2"><strong>Date:</strong> {{ formatDate(report.created_at) }}</p>
                        <p class="mb-2"><strong>Motif:</strong> {{ formatReason(report.reason) }}</p>
                        <p class="mb-2"><strong>Description:</strong> {{ report.description || 'Aucune description fournie' }}</p>
                        <p class="mb-2">
                            <strong>Statut:</strong> 
                            <span :class="['status-badge', report.status]">
                                {{ formatStatus(report.status) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-4" v-if="report?.status === 'pending'">
                <button @click="$emit('close')" class="btn-cancel">
                    Annuler
                </button>
                <button @click="$emit('resolve')" class="btn-action bg-green-600 hover:bg-green-700">
                    <i class="fas fa-check mr-2"></i>
                    Marquer comme traité
                </button>
                <button @click="$emit('warn')" class="btn-action bg-yellow-500 hover:bg-yellow-600">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Avertir l'utilisateur
                </button>
                <button @click="$emit('delete')" class="btn-action bg-red-600 hover:bg-red-700">
                    <i class="fas fa-trash mr-2"></i>
                    Supprimer le contenu
                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import Modal from './Modal.vue';

const props = defineProps({
    show: Boolean,
    report: Object
});

const emit = defineEmits(['close', 'resolve', 'warn', 'delete']);

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const formatContentType = (type) => {
    const typeMap = {
        'App\\Models\\Post': 'Réponse',
        'App\\Models\\Comment': 'Commentaire'
    };
    return typeMap[type] || type;
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

const formatStatus = (status) => {
    const statusMap = {
        pending: 'En attente',
        resolved: 'Traité',
        dismissed: 'Rejeté'
    };
    return statusMap[status] || status;
};
</script>

<style scoped>
.report-details-modal {
    max-width: 700px;
    margin: 0 auto;
}

.info-group {
    margin-bottom: 1.5rem;
}

.btn-action {
    @apply px-4 py-2 text-white rounded-lg text-sm font-medium;
}

.status-badge {
    @apply px-2 py-1 text-xs font-medium rounded-full;
}

.status-badge.pending {
    @apply bg-yellow-100 text-yellow-800;
}

.status-badge.resolved {
    @apply bg-green-100 text-green-800;
}

.status-badge.dismissed {
    @apply bg-red-100 text-red-800;
}

.close-btn {
    @apply p-2 hover:bg-gray-100 rounded-full transition-colors;
}

.btn-cancel {
    @apply px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-medium transition-colors;
}
</style>
