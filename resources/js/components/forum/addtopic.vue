<template>
    <div class="mt-2 formulaire">
        <div class="row">
            <div class="col-12 mb-4">
                <h4 class="">Création d'un nouveau sujet</h4>
                <p class="text-orange fw-bold">*Représente un champ obligatoire</p>
            </div>

            <div class="col-12 mb-4">
                <label class="form-label">Titre du sujet *</label>
                <input 
                    type="text" 
                    class="form-control" 
                    v-model="title"
                    placeholder="Entrez le titre de votre sujet"
                    required
                >
            </div>

            <div class="col-12 mb-4">
                <label class="form-label">Contenu *</label>
                <textarea 
                    class="form-control" 
                    v-model="content"
                    placeholder="Décrivez votre sujet en détail..."
                    rows="4"
                    required
                ></textarea>
            </div>

            <div class="col-12 mb-4">
                <label class="form-label">Tags (Recommandé)</label>
                <div class="tag-input-container">
                    <input 
                        type="text" 
                        class="form-control" 
                        v-model="tagInput"
                        @input="searchTags"
                        @keydown.enter.prevent="addTag"
                        placeholder="Appuyez sur Entrée pour ajouter un tag"
                    >
                    <div class="suggestions-dropdown" v-if="suggestedTags.length > 0">
                        <div v-for="(tag, index) in suggestedTags" 
                             :key="index" 
                             @click="selectTag(tag)"
                             class="suggestion-item">
                            <i class="bi bi-hash"></i>
                            <span>{{ tag.name }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-2 tag-list" v-if="tags.length > 0">
                    <span v-for="(tag, index) in tags" 
                          :key="index" 
                          class="badge bg-primary me-2">
                        #{{ tag }}
                        <i class="bi bi-x-circle ms-1" @click="removeTag(index)"></i>
                    </span>
                </div>
            </div>

            <div class="col-12 d-flex justify-content-end mt-4">
                <button class="btn btn-secondary me-2" @click="cancel">Annuler</button>
                <button class="btn btn-primary" @click="submit" :disabled="submitting">
                    {{ submitting ? 'Création...' : 'Créer le sujet' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            title: '',
            content: '',
            tags: [],
            tagInput: '',
            submitting: false,
            suggestedTags: []
        };
    },
    computed: {
        forumId() {
            // Récupérer l'ID du forum depuis l'URL
            const path = window.location.pathname;
            const matches = path.match(/\/forums\/(\d+)\/topics\/create/);
            return matches ? matches[1] : null;
        }
    },
    methods: {
        async searchTags() {
            if (this.tagInput.length > 0) {
                try {
                    const response = await fetch(`/tags/search?query=${this.tagInput}`);
                    const data = await response.json();
                    this.suggestedTags = data.slice(0, 5);
                } catch (error) {
                    console.error('Erreur lors de la recherche des tags:', error);
                    this.suggestedTags = [];
                }
            } else {
                this.suggestedTags = [];
            }
        },
        addTag() {
            const tag = this.tagInput.trim();
            if (tag && !this.tags.includes(tag) && this.tags.length < 5) {
                this.tags.push(tag);
                this.tagInput = '';
            }
        },
        removeTag(index) {
            this.tags.splice(index, 1);
        },
        selectTag(tag) {
            if (!this.tags.includes(tag.name) && this.tags.length < 5) {
                this.tags.push(tag.name);
            }
            this.tagInput = '';
            this.suggestedTags = [];
        },
        async submit() {
            if (!this.title || !this.content) {
                alert('Veuillez remplir tous les champs obligatoires');
                return;
            }

            this.submitting = true;
            try {
                const response = await axios.post(`/forums/${this.forumId}/topics`, {
                    title: this.title,
                    content: this.content,
                    tags: JSON.stringify(this.tags),
                    forum_id: this.forumId // Ajout de l'ID du forum
                }, {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.data.success) {
                    this.$toast?.success('Sujet créé avec succès');
                    window.location.href = response.data.redirect;
                }
            } catch (error) {
                console.error('Erreur:', error);
                this.$toast?.error('Une erreur est survenue lors de la création du sujet.');
            } finally {
                this.submitting = false;
            }
        },
        cancel() {
            window.location.href = `/forums/${this.forumId}`;
        }
    }
};
</script>

<style scoped>
.text-orange {
    color: #fd7e14;
}
.checkBtn {
    border: 1px solid #ddd;
    transition: all 0.3s;
}
input[type="radio"]:checked + .checkBtn {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
}
.rules-link {
    background: none;
    border: none;
    color: var(--primary);
    cursor: pointer;
    font-weight: 600;
    padding: 0;
    text-decoration: underline;
}

.tag-input-container {
    position: relative;
}

.suggestions-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
}

.suggestion-item {
    padding: 8px 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background-color 0.2s;
}

.suggestion-item:hover {
    background-color: #f8f9fa;
}

.tag-list .badge {
    font-size: 0.9em;
    padding: 6px 12px;
}

.tag-list .badge i {
    cursor: pointer;
    opacity: 0.7;
}

.tag-list .badge i:hover {
    opacity: 1;
}
</style>