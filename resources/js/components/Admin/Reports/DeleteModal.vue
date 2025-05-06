<template>
    <div v-if="show" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>{{ title || 'Confirmer la suppression' }}</h3>
                <button class="close-button" @click="$emit('close')">×</button>
            </div>
            <div class="modal-body">
                <p>{{ message || 'Êtes-vous sûr de vouloir supprimer cet élément ?' }}</p>
                <p class="warning">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button class="btn cancel" @click="$emit('close')">Annuler</button>
                <button class="btn delete" @click="$emit('confirm')">Supprimer</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'DeleteModal',
    props: {
        show: Boolean,
        title: String,
        message: String
    }
}
</script>



<style scoped>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    border-radius: 12px;
    width: 90%;
    max-width: 400px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.modal-header {
    padding: 1rem;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.warning {
    color: #d33;
    font-size: 0.9rem;
}

.modal-footer {
    padding: 1rem;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
}

.btn.cancel {
    background: #e0e0e0;
}

.btn.delete {
    background: #d33;
    color: white;
}
</style>


