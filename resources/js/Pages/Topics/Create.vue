<template>
    <MainLayout :showNavbar="false" :showSidebar="true">
        <div class="create-forum-container">
            <div class="forum-card">
                <div class="forum-header">
                    <h1 class="forum-title">Créer un nouveau sujet</h1>
                    <p class="forum-subtitle">Partagez vos questions et réflexions avec la communauté.</p>
                </div>

                <form @submit.prevent="createTopic" class="forum-form">
                    <!-- Informations de base -->
                    <div class="form-section">
                        <h2 class="section-title">Informations de base</h2>
                        
                        <div class="form-group">
                            <label for="topic-title">Titre du sujet (*) </label>
                            <input 
                                id="topic-title"
                                v-model="title" 
                                placeholder="Donnez un titre à votre sujet..." 
                                required 
                                class="form-control"
                            />
                        </div>
                        
                        <div class="form-group">
                            <label for="topic-content">Contenu du message (*) </label>
                            <QuillEditor 
                                v-model="content" 
                                id="topic-content" 
                                class="form-control textarea" 
                                placeholder="Détaillez votre sujet ici..." 
                                required 
                            ></QuillEditor>
                        </div>
                    </div>

                    <!-- Tags -->
                    <div class="form-section">
                        <h2 class="section-title">Tags</h2>
                        <p class="section-description">Ajoutez des hashtags pour faciliter la découverte de votre sujet (recommandé par 5)</p>

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

                    <!-- Notifications et Règles -->
                    <div class="form-section">
                        <div class="form-group">
                            <div class="form-check">
                                <input v-model="notify" type="checkbox" id="topic-notify" class="form-check-input custom-checkbox" />
                                <label for="topic-notify" class="form-check-label">
                                    M'informer des nouvelles réponses par email
                                </label>
                            </div>

                            <div class="form-check">
                                <input v-model="rulesAccepted" type="checkbox" id="topic-rules" class="form-check-input custom-checkbox" required />
                                <label for="topic-rules" class="form-check-label">
                                    J'ai lu et j'accepte les <button type="button" @click="showRulesModal" class="rules-link">règles du forum</button> *
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" @click="cancel">Annuler</button>
                        <button type="submit" class="btn btn-primary">Publier le sujet</button>
                    </div>
                    <div class="form-note">
                        <i class="fas fa-info-circle"></i> Les champs marqués d'un astérisque (*) sont obligatoires.
                    </div>
                </form>  
            </div>
        </div>

        <SujetRulesModal 
            :is-open="isRulesModalOpen"
            @close="closeRulesModal"
            @accept="acceptRules"
        />
    </MainLayout>
</template>

<script>
import MainLayout from '../../Layouts/Main.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3'; // Importer le router
import '../../../css/styles/CreateSujet.css';
import QuillEditor from '../../Components/Forum/QuillEditor.vue';
import SujetRulesModal from '../../Components/Forum/SujetRuleModal.vue'; // Assurez-vous que le chemin est correct

export default {
    components: {
        MainLayout,
        QuillEditor,
        SujetRulesModal,
    },
    props: {
        forum: Object, // Assurez-vous que le forum est passé en props
    },
    setup(props) {
        const title = ref('');
        const content = ref('');
        const tagInput = ref('');
        const tags = ref([]);
        const suggestedTags = ref([]);
        const notify = ref(false);
        const rulesAccepted = ref(false);
        const forumId = ref(props.forum.id); // Récupération de l'ID du forum
        const isRulesModalOpen = ref(false);

        const createTopic = () => {
            router.post(`/forums/${forumId.value}/topics`, {
                title: title.value,
                content: content.value,
                tags: JSON.stringify(tags.value) // Inclure les tags dans les données envoyées
            });
        };

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

        const cancel = () => {
            router.get(`/forums/${forumId.value}`);
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

        return {
            title,
            content,
            tagInput,
            tags,
            suggestedTags,
            notify,
            rulesAccepted,
            addTag,
            searchTags,
            selectTag,
            removeTag,
            createTopic,
            cancel, // Ajout de la fonction cancel
            isRulesModalOpen,
            showRulesModal,
            closeRulesModal,
            acceptRules,
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
</style>
