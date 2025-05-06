<template>
    <div class="mt-2 formulaire">
        <div class="row">
            <div class="col-12 mb-4">
                <h4 class="">Informations générales</h4>
                <p class="text-orange fw-bold">*Représente un champ obligatoire</p>
            </div>

            <div class="col-12 mb-4">
                <label class="form-label">Titre de la discussion *</label>
                <input 
                    type="text" 
                    class="form-control" 
                    v-model="name"
                    placeholder="Exemple : Discussion sur l'innovation"
                    required
                >
            </div>

            <div class="col-12 mb-4">
                <label class="form-label">Description *</label>
                <textarea 
                    class="form-control" 
                    v-model="description"
                    placeholder="Décrivez votre forum en quelques lignes..."
                    rows="4"
                    required
                ></textarea>
            </div>

            <div class="col-lg-6 col-md-6 col-12 mb-4">
                <label class="form-label">Catégorie *</label>
                <select class="form-select" v-model="category">
                    <option value="" disabled selected>Choisissez une catégorie...</option>
                    <option value="idées_innovation">Idées & Innovation</option>
                    <option value="modeles_economiques">Modèles Économiques & Stratégies</option>
                    <option value="incubation_acceleration">Incubation & Accélération</option>
                    <option value="coaching_ia">Coaching & IA</option>
                    <option value="levee_fonds">Levée de Fonds</option>
                    <option value="tech_developpement">Tech & Développement Produit</option>
                    <option value="marketing_growth">Marketing, Acquisition & Croissance</option>
                    <option value="aspects_legaux">Aspects Légaux & Réglementation</option>
                    <option value="evenements_reseau">Événements, Réseau & Partenariats</option>
                    <option value="success_stories">Témoignages & Success Stories</option>
                </select>
            </div>

            <div class="col-lg-6 col-md-6 col-12 mb-4">
                <label class="form-label">Visibilité *</label>
                <div class="row">
                    <div class="col-6">
                        <input type="radio" id="public" value="public" v-model="visibility" class="d-none" required>
                        <label for="public" class="btn checkBtn w-100">Public</label>
                    </div>
                    <div class="col-6">
                        <input type="radio" id="private" value="private" v-model="visibility" class="d-none">
                        <label for="private" class="btn checkBtn w-100">Privé</label>
                    </div>
                </div>
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

            <div class="col-12 mb-4">
                <label class="form-label">Image de couverture</label>
                <div class="jointe text-center">
                    <input type="file" 
                           id="file" 
                           class="d-none" 
                           @change="onFileChange"
                           accept="image/*">
                    <label for="file" class="">
                        <svg class="mb-2" xmlns="http://www.w3.org/2000/svg" width="71" height="53" viewBox="0 0 71 53"
                            fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M20.917 11.6665C18.5963 11.6665 16.3708 12.5884 14.7298 14.2293C13.0889 15.8703 12.167 18.0959 12.167 20.4165C12.167 22.7371 13.0889 24.9627 14.7298 26.6037C16.3708 28.2446 18.5963 29.1665 20.917 29.1665C23.2376 29.1665 25.4632 28.2446 27.1042 26.6037C28.7451 24.9627 29.667 22.7371 29.667 20.4165C29.667 18.0959 28.7451 15.8703 27.1042 14.2293C25.4632 12.5884 23.2376 11.6665 20.917 11.6665ZM18.0003 20.4165C18.0003 19.643 18.3076 18.9011 18.8546 18.3541C19.4016 17.8071 20.1434 17.4998 20.917 17.4998C21.6905 17.4998 22.4324 17.8071 22.9794 18.3541C23.5264 18.9011 23.8337 19.643 23.8337 20.4165C23.8337 21.1901 23.5264 21.9319 22.9794 22.4789C22.4324 23.0259 21.6905 23.3332 20.917 23.3332C20.1434 23.3332 19.4016 23.0259 18.8546 22.4789C18.3076 21.9319 18.0003 21.1901 18.0003 20.4165Z"
                                fill="#0068A9" />
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M9.25 0C6.92936 0 4.70376 0.921873 3.06282 2.56282C1.42187 4.20376 0.5 6.42936 0.5 8.75L0.5 43.75C0.5 46.0706 1.42187 48.2962 3.06282 49.9372C4.70376 51.5781 6.92936 52.5 9.25 52.5H61.75C64.0706 52.5 66.2962 51.5781 67.9372 49.9372C69.5781 48.2962 70.5 46.0706 70.5 43.75V8.75C70.5 6.42936 69.5781 4.20376 67.9372 2.56282C66.2962 0.921873 64.0706 0 61.75 0H9.25ZM61.75 5.83333H9.25C8.47645 5.83333 7.73459 6.14062 7.18761 6.68761C6.64062 7.23459 6.33333 7.97645 6.33333 8.75V43.75C6.33333 44.5235 6.64062 45.2654 7.18761 45.8124C7.73459 46.3594 8.47645 46.6667 9.25 46.6667H21.8325L41.8933 26.6029C42.7059 25.7902 43.6705 25.1456 44.7322 24.7058C45.7939 24.266 46.9319 24.0396 48.081 24.0396C49.2302 24.0396 50.3682 24.266 51.4299 24.7058C52.4916 25.1456 53.4562 25.7902 54.2687 26.6029L64.6667 37.0008V8.75C64.6667 7.97645 64.3594 7.23459 63.8124 6.68761C63.2654 6.14062 62.5235 5.83333 61.75 5.83333ZM61.75 46.6667H30.0808L46.0204 30.73C46.5674 30.1832 47.3091 29.876 48.0825 29.876C48.8559 29.876 49.5976 30.1832 50.1446 30.73L64.3954 44.9808C64.1609 45.4847 63.7873 45.911 63.3186 46.2097C62.85 46.5083 62.3057 46.6669 61.75 46.6667Z"
                                fill="#0068A9" />
                        </svg><br>
                        Cliquez pour sélectionner une image
                    </label>
                    <small>( JPG, PNG, max 2MB)</small>
                    <p>{{ fileName }}</p>
                    <img v-if="imagePreview" :src="imagePreview" class="mt-2" style="max-width: 200px;">
                </div>
            </div>

                  <!-- Notifications et Règles -->
                  <div class="form-section">
                        <div class="form-group">
                          

                            <div class="form-check">
                                <input v-model="rulesAccepted" type="checkbox" id="forum-rules" class="form-check-input custom-checkbox" required />
                                <label for="forum-rules" class="form-check-label">
                                    J'ai lu et j'accepte les <button type="button" @click="showRulesModal" class="rules-link">règles du forum</button> *
                                </label>
                            </div>
                        </div>
                    </div>
            <div class="col-12 d-flex justify-content-end mt-4">
                <button class="btn btn-secondary me-2" @click="cancel">Annuler</button>
                <button class="btn btn-primary" @click="submit" :disabled="submitting">
                    {{ submitting ? 'Création...' : 'Créer le forum' }}
                </button>
            </div>
        </div>
    </div>
    <ForumRulesModal 
            :is-open="isRulesModalOpen"
            @close="closeRulesModal"
            @accept="acceptRules"
        />
</template>

<script>
import ForumRulesModal from './ForumRulesModal.vue';

export default {
    components: {
        ForumRulesModal
    },
    data() {
        return {
            name: '',
            description: '',
            category: '',
            visibility: 'public',
            tags: [],
            tagInput: '',
            fileName: '',
            imageFile: null,
            imagePreview: null,
            submitting: false,
            rulesAccepted: false,
            isRulesModalOpen: false,
            suggestedTags: []
        };
    },
    methods: {
        onFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('L\'image est trop volumineuse. Taille maximale: 2Mo');
                    return;
                }
                this.fileName = file.name;
                this.imageFile = file;
                this.createImagePreview(file);
            }
        },
        createImagePreview(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.imagePreview = e.target.result;
            };
            reader.readAsDataURL(file);
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
        async searchTags() {
            if (this.tagInput.length > 0) {
                try {
                    const response = await fetch(`/tags/search?query=${this.tagInput}`);
                    const data = await response.json();
                    this.suggestedTags = data.slice(0, 5); // Limite à 5 suggestions
                } catch (error) {
                    console.error('Erreur lors de la recherche des tags:', error);
                    this.suggestedTags = [];
                }
            } else {
                this.suggestedTags = [];
            }
        },
        selectTag(tag) {
            if (!this.tags.includes(tag.name) && this.tags.length < 5) {
                this.tags.push(tag.name);
            }
            this.tagInput = '';
            this.suggestedTags = [];
        },
        showRulesModal() {
            this.isRulesModalOpen = true;
        },
        closeRulesModal() {
            this.isRulesModalOpen = false;
        },
        acceptRules() {
            this.rulesAccepted = true;
            this.isRulesModalOpen = false;
        },
        async submit() {
            this.submitting = true;
            try {
                const formData = new FormData();
                formData.append('name', this.name);
                formData.append('description', this.description);
                formData.append('visibility', this.visibility);
                formData.append('category', this.category);
                formData.append('tags', JSON.stringify(this.tags));
                formData.append('rulesAccepted', this.rulesAccepted);

                if (this.imageFile) {
                    formData.append('image', this.imageFile);
                }

                const response = await axios.post('/forums', formData);
                this.$toast.success('Forum créé avec succès');
                window.location.href = '/forums';
            } catch (error) {
                console.error('Erreur:', error);
                this.$toast.error('Une erreur est survenue lors de la création du forum.');
            } finally {
                this.submitting = false;
            }
        },
        cancel() {
            window.location.href = '/forums';
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