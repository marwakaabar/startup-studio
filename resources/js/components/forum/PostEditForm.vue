<template>
    <div class="post-edit-form">
        <textarea 
            v-model="editedContent"
            class="post-input"
            placeholder="Modifier votre message..."
            @input="validateContent"
        ></textarea>
        <div class="validation-message" v-if="validationError">
            {{ validationError }}
        </div>
        <div class="form-actions">
            <button 
                @click="handleSave" 
                class="btn primary"
                :disabled="!isValid || !hasChanged"
            >
                Sauvegarder
            </button>
            <button @click="$emit('cancel')" class="btn secondary">
                Annuler
            </button>
        </div>
    </div>
</template>

<script>
export default {
    name: 'PostEditForm',
    props: {
        initialContent: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            editedContent: this.initialContent,
            validationError: '',
            isValid: true
        }
    },
    computed: {
        hasChanged() {
            return this.editedContent !== this.initialContent;
        }
    },
    methods: {
        validateContent() {
            if (!this.editedContent.trim()) {
                this.validationError = 'Le message ne peut pas être vide';
                this.isValid = false;
            } else if (this.editedContent.length < 10) {
                this.validationError = 'Le message doit contenir au moins 10 caractères';
                this.isValid = false;
            } else {
                this.validationError = '';
                this.isValid = true;
            }
        },
        handleSave() {
            if (this.isValid && this.hasChanged) {
                this.$emit('save', this.editedContent);
            }
        }
    }
}
</script>

<style scoped>
.post-edit-form {
    margin: 1rem 0;
}

.post-input {
    width: 100%;
    min-height: 120px;
    padding: 1rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-bottom: 1rem;
    resize: vertical;
    font-family: inherit;
    font-size: 1rem;
}

.post-input:focus {
    outline: none;
    border-color: #0093ee;
    box-shadow: 0 0 0 2px rgba(0, 147, 238, 0.1);
}

.validation-message {
    color: #d33;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

.btn {
    padding: 0.5rem 1.5rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s;
}

.btn.primary {
    background: #0093ee;
    color: white;
}

.btn.primary:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.btn.secondary {
    background: #e0e0e0;
    color: #333;
}

.btn:hover:not(:disabled) {
    opacity: 0.9;
}
</style>
