<template>
    <MainLayout :showNavbar="false" :showSidebar="true">
        <div class="create-forum-container">
            <div class="forum-card">
                <div class="forum-header">
                    <h1 class="forum-title">Créer un nouveau forum</h1>
                    <p class="forum-subtitle">Partagez vos passions avec la communauté en créant un forum thématique.</p>
                </div>

                <form @submit.prevent="submit" class="forum-form">
                    <!-- Informations de base -->
                    <div class="form-section">
                        <h2 class="section-title">Informations de base</h2>
                        
                        <div class="form-group">
                            <label for="forum-title">Titre du forum (*) </label>
                            <input 
                                id="forum-title"
                                v-model="name" 
                                placeholder="Donnez un titre à votre forum..." 
                                required 
                                class="form-control"
                            />
                        </div>
                        
                     
                        
                        <div class="form-group">
                            <label for="forum-description">Description (*) </label>
                            <textarea 
                                id="forum-description"
                                v-model="description" 
                                placeholder="Décrivez votre forum en quelques lignes..." 
                                required 
                                class="form-control textarea"
                            ></textarea>
                        </div>
                    </div>
                    <!-- Category -->

                    <div class="form-section">
                        <h2 class="section-title">Catégorie du forum (*) </h2>
                        
                        <div class="form-group">
                          
                           <select 
                            id="forum-category" 
                            v-model="category" 
                            class="form-control"
                            required
                        >
                            <option value="" disabled selected>Choisissez une catégorie...</option>
                            <option value="idées_innovation">Idées & Innovation</option>
                            <option value="modeles_economiques">Modèles Économiques & Stratégies</option>
                            <option value="incubation_acceleration">Incubation & Accélération Startups</option>
                            <option value="coaching_ia">Coaching, Mentorat & IA Assistée</option>
                            <option value="levee_fonds">Levée de Fonds & Investisseurs</option>
                            <option value="tech_developpement">Tech & Développement Produit</option>
                            <option value="marketing_growth">Marketing, Acquisition & Croissance</option>
                            <option value="aspects_legaux">Aspects Légaux & Réglementation</option>
                            <option value="evenements_reseau">Événements, Réseau & Partenariats</option>
                            <option value="success_stories">Témoignages & Success Stories</option>
                        </select>
                        </div>
                    </div>

                    <!-- Tags -->
                    <div class="form-section">
                        <h2 class="section-title">Tags</h2>
                        <p class="section-description">Ajoutez des hashtags pour faciliter la découverte de votre forum (recommandé par 5)</p>

                        <div class="form-group">
                            <input 
                                v-model="tagInput" 
                                @input="searchTags" 
                                @keydown.enter.prevent="addTag" 
                                placeholder="Ajouter des hashtags..." 
                                class="form-control" 
                            />

                            <!-- Afficher les hashtags suggérés -->
                            <div class="suggestions" v-if="suggestedTags.length > 0">
                                <ul>
                                    <li v-for="(tag, index) in suggestedTags" :key="index" 
                                        @click="selectTag(tag)" 
                                        class="suggestion-item">#{{ tag.name }}</li>
                                </ul>
                            </div>

                            <!-- Les hashtags sélectionnés apparaîtront ici -->
                            <div class="tags-container" v-if="tags.length > 0">
                                <span v-for="(tag, index) in tags" :key="index" class="tag">
                                    #{{ tag }}
                                    <button type="button" class="tag-remove" @click="removeTag(index)">×</button>
                                </span>
                            </div>
                        </div>
                    </div>


                    <!-- Image du forum -->
                    <div class="form-section">
                        <h2 class="section-title">Image du forum</h2>
                        
                        <div class="form-group">
                            <div class="image-upload-container">
                                <div 
                                    class="image-upload-area"
                                    @click="triggerFileInput"
                                    @dragover.prevent="onDragOver"
                                    @dragleave.prevent="onDragLeave"
                                    @drop.prevent="onDrop"
                                    :class="{ 'drag-over': isDragging }"
                                >
                                    <div v-if="!imagePreview" class="upload-placeholder">
                                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                        <p>Glissez-déposez une image ici</p>
                                        <button type="button" class="btn btn-secondary">Parcourir</button>
                                    </div>
                                    <div v-else class="image-preview">
                                        <img :src="imagePreview" alt="Aperçu de l'image" />
                                        <button type="button" class="btn btn-danger remove-image" @click.stop="removeImage">
                                            Supprimer
                                        </button>
                                    </div>
                                </div>
                                <input 
                                    type="file" 
                                    ref="fileInput" 
                                    accept="image/*" 
                                    @change="onFileChange" 
                                    class="file-input"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Paramètres de confidentialité -->
                    <div class="form-section">
                        <h2 class="section-title">Paramètres de confidentialité (*)</h2>
                        <p class="section-question">Qui peut voir et participer à ce forum ?</p>
                        
                        <div class="visibility-options" >
                            <label class="visibility-option">
                                <input type="radio" v-model="visibility" value="public" required />
                                <span class="visibility-radio"></span>
                                <div class="visibility-label">
                                    <i class="fas fa-globe-americas visibility-icon"></i>
                                    <span>Public</span>
                                </div>
                            </label>
                            
                            <label class="visibility-option">
                                <input type="radio" v-model="visibility" value="private" />
                                <span class="visibility-radio"></span>
                                <div class="visibility-label">
                                    <i class="fas fa-lock visibility-icon"></i>
                                    <span>Privé</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Aperçu et boutons -->
                    <div class="form-section">
                        <details class="preview-details">
                            <summary class="preview-summary">
                                <i class="fas fa-eye"></i> Aperçu en direct de votre forum
                            </summary>
                            <div class="forum-preview">
                                <h3 class="preview-title">{{ name || 'Titre du forum' }}</h3>
                                <p class="preview-description">{{ description || 'Description du forum' }}</p>
                                <div class="preview-meta">
                                    <span class="preview-visibility" v-if="visibility">
                                        <i :class="visibility === 'public' ? 'fas fa-globe-americas' : 'fas fa-lock'"></i>
                                        {{ visibility === 'public' ? 'Public' : 'Privé' }}
                                    </span>
                                </div>
                                <div class="preview-tags" v-if="tags.length > 0">
                                    <span v-for="(tag, index) in tags" :key="index" class="preview-tag">
                                        #{{ tag }}
                                    </span>
                                </div>
                            </div>
                        </details>
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

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" @click="cancel">Annuler</button>
                        <button type="submit" class="btn btn-primary">Créer le forum</button>
                    </div>
                    <div class="form-note">
                    <i class="fas fa-info-circle"></i> Les champs marqués d'un astérisque (*) sont obligatoires.
                    </div>
                </form>
            </div>
        </div>

        <ForumRulesModal 
            :is-open="isRulesModalOpen"
            @close="closeRulesModal"
            @accept="acceptRules"
        />
    </MainLayout>
</template>

<script>
import MainLayout from '../../Layouts/Main.vue';
import ForumRulesModal from '../../Components/Forum/ForumRulesModal.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import '../../../css/styles/CreateForum.css';

export default {
    components: {
        MainLayout,
        ForumRulesModal,
    },
    setup() {
        const name = ref('');
        const description = ref('');
        const visibility = ref('');
        const category = ref('');
        const tags = ref([]);
        const tagInput = ref('');
        const suggestedTags = ref([]);
        const fileInput = ref(null);
        const imageFile = ref(null);
        const imagePreview = ref('');
        const isDragging = ref(false);
        const notify = ref(false);
        const rulesAccepted = ref(false);
        const isRulesModalOpen = ref(false);

        const addTag = () => {
            const tag = tagInput.value.trim();
            if (tag && !tags.value.includes(tag) && tags.value.length < 5) {
                tags.value.push(tag);
                tagInput.value = '';
            }
        };

        const searchTags = async () => {
            if (tagInput.value.length > 0) {
                const response = await fetch(`/tags/search?query=${tagInput.value}`);
                suggestedTags.value = await response.json();
            } else {
                suggestedTags.value = [];
            }
        };

        const selectTag = (tag) => {
            const tagName = tag.name;
            if (!tags.value.includes(tagName) && tags.value.length < 5) {
                tags.value.push(tagName);
                tagInput.value = '';
                suggestedTags.value = [];
            }
        };

        const removeTag = (index) => {
            tags.value.splice(index, 1);
        };

        const triggerFileInput = () => {
            fileInput.value.click();
        };

        const onFileChange = (event) => {
            const file = event.target.files[0];
            if (file) {
                imageFile.value = file;
                createImagePreview(file);
            }
        };

        const createImagePreview = (file) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.value = e.target.result;
            };
            reader.readAsDataURL(file);
        };

        const removeImage = () => {
            imageFile.value = null;
            imagePreview.value = '';
            if (fileInput.value) {
                fileInput.value.value = '';
            }
        };

        const onDragOver = () => {
            isDragging.value = true;
        };

        const onDragLeave = () => {
            isDragging.value = false;
        };

        const onDrop = (event) => {
            isDragging.value = false;
            const file = event.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                imageFile.value = file;
                createImagePreview(file);
            }
        };

        const showRulesModal = () => {
            isRulesModalOpen.value = true;
        };

        const closeRulesModal = () => {
            isRulesModalOpen.value = false;
        };

        const acceptRules = () => {
            rulesAccepted.value = true;
            closeRulesModal();
        };

        const submit = () => {
            const formData = new FormData();
            formData.append('name', name.value);
            formData.append('description', description.value);
            formData.append('visibility', visibility.value);
            formData.append('category', category.value);
            formData.append('tags', JSON.stringify(tags.value));
            formData.append('notify', notify.value);
            formData.append('rulesAccepted', rulesAccepted.value);
            if (imageFile.value) {
                formData.append('image', imageFile.value);
            }

            router.post('/forums', formData, {
                onStart: () => console.log('Envoi en cours...'),
                onSuccess: () => console.log('Forum créé avec succès!'),
                onError: (errors) => console.error('Erreurs:', errors)
            });
        };

        const cancel = () => {
            router.get(`/forums`);
        };

        return {
            name,
            description,
            visibility,
            category,
            tags,
            tagInput,
            suggestedTags,
            fileInput,
            imageFile,
            imagePreview,
            isDragging,
            notify,
            rulesAccepted,
            isRulesModalOpen,
            addTag,
            searchTags,
            selectTag,
            removeTag,
            triggerFileInput,
            onFileChange,
            createImagePreview,
            removeImage,
            onDragOver,
            onDragLeave,
            onDrop,
            showRulesModal,
            closeRulesModal,
            acceptRules,
            submit,
            cancel,
        };
    },
};
</script>
<style scoped>
.rules-link {
    background: none;
    border: none;
    color: var(--primary);
    cursor: pointer;
    font-weight: 600;
    padding: 0;
    text-decoration: underline;
}

.preview-meta {
    margin: 10px 0;
    color: #666;
}

.preview-visibility {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 8px;
    background-color: #f0f0f0;
    border-radius: 4px;
    font-size: 0.9em;
}

.preview-visibility i {
    font-size: 0.9em;
}
</style>