<template>
    <div class="search-container">
        <form class="search-form" @submit.prevent="performSearch">
            <input 
                type="text" 
                class="search-input" 
                v-model="searchQuery"
                placeholder="Rechercher des discussions..."
            >
            <button type="submit" class="search-btn">
                <i class="fas fa-search"></i> Rechercher
            </button>
        </form>
        <div class="filters">
            <select v-model="selectedCategory" class="filter-select" @change="performSearch">
                <option value="">Toutes les catégories</option>
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
            <select v-model="selectedSort" class="filter-select" @change="performSearch">
                <option value="recent">Plus récents</option>
                <option value="popular">Plus populaires</option>
                <option value="comments">Plus commentés</option>
            </select>
            <select v-model="selectedHashtag" class="filter-select" @change="performSearch">
                <option value="">Tous les hashtags</option>
                <option v-for="hashtag in hashtags" :key="hashtag.id" :value="hashtag.name">
                    #{{ hashtag.name }}
                </option>
            </select>
            <button type="button" class="ai-recommend-btn" title="Recommandations IA">
                <i class="fas fa-magic"></i>Recommandation IA
            </button>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, watch } from 'vue';

export default {
    name: 'SearchComponent',
    emits: ['search', 'filter'],
    setup(props, { emit }) {
        const searchQuery = ref('');
        const selectedCategory = ref('');
        const selectedSort = ref('recent');
        const selectedHashtag = ref('');
        const hashtags = ref([]);

        const fetchHashtags = async () => {
            try {
                const response = await fetch('/hashtags');
                const data = await response.json();
                hashtags.value = data;
            } catch (error) {
                console.error('Erreur lors du chargement des hashtags:', error);
            }
        };

        const performSearch = () => {
            emit('search', {
                query: searchQuery.value,
                category: selectedCategory.value,
                sortBy: selectedSort.value,
                hashtag: selectedHashtag.value
            });
        };

        onMounted(() => {
            fetchHashtags();
        });

        watch([searchQuery, selectedCategory, selectedSort, selectedHashtag], () => {
            performSearch();
        });

        return {
            searchQuery,
            selectedCategory,
            selectedSort,
            selectedHashtag,
            hashtags,
            performSearch
        };
    }
};
</script>

<style scoped>

</style>
