<template>
    <div v-if="show" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>{{ isAlreadyReported ? 'Contenu déjà signalé' : 'Signaler le contenu' }}</h3>
                <button class="close-button" @click="$emit('close')">×</button>
            </div>
            
            <template v-if="isAlreadyReported">
                <div class="modal-body already-reported">
                    <div class="info-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <p>Vous avez déjà signalé ce contenu. Notre équipe de modération va examiner votre signalement dans les plus brefs délais.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn primary" @click="$emit('close')">Compris</button>
                </div>
            </template>

            <template v-else>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Raison du signalement </label>
                        <select v-model="reason" class="form-control" :class="{ 'error': errors.reason }">
                            <option value="">Sélectionnez une raison</option>
                            <option value="spam">Spam</option>
                            <option value="inappropriate">Contenu inapproprié</option>
                            <option value="offensive">Contenu offensant</option>
                            <option value="other">Autre</option>
                        </select>
                        <span v-if="errors.reason" class="error-message">{{ errors.reason }}</span>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea 
                            v-model="description" 
                            class="form-control"
                            :class="{ 'error': errors.description }"
                            placeholder="Décrivez la raison de votre signalement (minimum 10 caractères)"
                        ></textarea>
                        <span v-if="errors.description" class="error-message">{{ errors.description }}</span>
                        <div class="char-count" :class="{ 'error': description.length > 1000 }">
                            {{ description.length }}/1000
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn cancel" @click="$emit('close')">Annuler</button>
                    <button 
                        class="btn submit" 
                        @click="validateAndSubmit"
                        :disabled="!reason || !description.trim() || description.length > 1000"
                    >
                        Signaler
                    </button>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'ReportModal',
    props: {
        show: Boolean,
        type: String,
        id: [Number, String]
    },
    data() {
        return {
            reason: '',
            description: '',
            isAlreadyReported: false,
            errors: {
                reason: '',
                description: ''
            }
        }
    },
    computed: {
        isValid() {
            return this.reason && this.description.length >= 10;
        }
    },
    methods: {
        async submitReport() {
            try {
                const response = await axios.post('/reports', {
                    reportable_type: this.type,
                    reportable_id: this.id,
                    reason: this.reason,
                    description: this.description
                });

                if (response.data.success) {
                    this.$emit('reported', response.data);
                    this.$emit('close');
                    this.resetForm();
                }
            } catch (error) {
                if (error.response?.status === 422) {
                    this.isAlreadyReported = true;
                } else {
                    console.error('Error submitting report:', error);
                }
            }
        },
        validateAndSubmit() {
            // Réinitialiser les erreurs
            this.errors = {
                reason: '',
                description: ''
            };

            // Valider la raison
            if (!this.reason) {
                this.errors.reason = 'Veuillez sélectionner une raison';
            }

            // Valider la description
            if (!this.description.trim()) {
                this.errors.description = 'La description est requise';
            } else if (this.description.length < 10) {
                this.errors.description = 'La description doit contenir au moins 10 caractères';
            } else if (this.description.length > 1000) {
                this.errors.description = 'La description ne doit pas dépasser 1000 caractères';
            }

            // Si pas d'erreurs, soumettre
            if (!this.errors.reason && !this.errors.description) {
                this.submitReport();
            }
        },
        resetForm() {
            this.reason = '';
            this.description = '';
            this.errors = {
                reason: '',
                description: ''
            };
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
    max-width: 500px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    color: #005183;
}

.close-button {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #666;
}

.modal-body {
    padding: 1.5rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 1rem;
}

textarea.form-control {
    min-height: 100px;
    resize: vertical;
}

.form-control.error {
    border-color: #dc3545;
}

.error-message {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
    display: block;
}

.char-count {
    text-align: right;
    font-size: 0.875rem;
    color: #666;
    margin-top: 0.25rem;
}

.char-count.error {
    color: #dc3545;
}

.form-group label::after {
    content: " *";
    color: #dc3545;
}

.modal-footer {
    padding: 1.5rem;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

.btn {
    padding: 0.5rem 1.5rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
}

.btn.cancel {
    background: #e0e0e0;
    color: #333;
}

.btn.submit {
    background: #0093ee;
    color: white;
}

.btn.submit:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.already-reported {
    text-align: center;
    padding: 2rem;
}

.info-icon {
    font-size: 3rem;
    color: #0093ee;
    margin-bottom: 1rem;
}

.already-reported p {
    color: #666;
    line-height: 1.5;
    margin: 0;
}

.modal-footer .btn.primary {
    background: #0093ee;
    color: white;
    width: 100%;
}
</style>
