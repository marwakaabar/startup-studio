<template>
    <div v-if="show" class="modal-overlay" @click.self="$emit('close')">
        <div class="modal-container">
            <div class="modal-header">
                <div class="warning-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3>Confirmer la suppression</h3>
                <button @click="$emit('close')" class="close-button">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p class="message">Êtes-vous sûr de vouloir supprimer ceci ?</p>
                <div class="warning-box">
                    <i class="fas fa-info-circle"></i>
                    <p class="warning-text">Cette action est irréversible et supprimera définitivement ce commentaire.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button @click="$emit('close')" class="btn-cancel">
                    <i class="fas fa-arrow-left"></i> Annuler
                </button>
                <button @click="$emit('confirm')" class="btn-delete">
                    <i class="fas fa-trash-alt"></i> Supprimer
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        show: {
            type: Boolean,
            default: false
        }
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
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    backdrop-filter: blur(5px);
    animation: fadeIn 0.2s ease-out;
}

.modal-container {
    background: white;
    border-radius: 16px;
    width: 90%;
    max-width: 480px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    animation: slideUp 0.3s ease-out;
    overflow: hidden;
}

.modal-header {
    padding: 24px;
    background: #f8fafc;
    display: flex;
    align-items: center;
    gap: 16px;
}

.warning-icon {
    background: #fee2e2;
    color: #dc2626;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.modal-header h3 {
    margin: 0;
    color: #1e293b;
    font-size: 1.25rem;
    flex-grow: 1;
}

.close-button {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: none;
    background: #f1f5f9;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.close-button:hover {
    background: #e2e8f0;
    color: #475569;
}

.modal-body {
    padding: 24px;
}

.message {
    color: #334155;
    font-size: 1rem;
    margin-bottom: 16px;
}

.warning-box {
    background: #fffbeb;
    border: 1px solid #fef3c7;
    border-radius: 8px;
    padding: 12px 16px;
    display: flex;
    gap: 12px;
    align-items: start;
}

.warning-box i {
    color: #d97706;
    margin-top: 3px;
}

.warning-text {
    color: #92400e;
    font-size: 0.875rem;
    margin: 0;
    line-height: 1.5;
}

.modal-footer {
    padding: 16px 24px;
    background: #f8fafc;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.btn-delete, .btn-cancel {
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    border: none;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}

.btn-delete {
    background: #ef4444;
    color: white;
}

.btn-delete:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

.btn-cancel {
    background: white;
    color: #475569;
    border: 1px solid #e2e8f0;
}

.btn-cancel:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
</style>
