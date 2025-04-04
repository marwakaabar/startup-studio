<template>
    <MainLayout :showNavbar="false" :showSidebar="true">
        <main class="main-content">
            <h1 class="page-title">Discussions populaires</h1>

            <!-- Search and Filters Component -->
            <SearchComponent @search="handleSearch" @filter="handleFilter" />

            <Link 
                v-if="$page.props.auth.user.role !== 'investisseur' && $page.props.auth.user.role !== 'coach'"
                href="/forums/create" 
                class="btn btn-primary mb-4"
            >Créer un Forum</Link>

            <!-- Message aucun résultat -->
            <div v-if="showNoResults" class="no-results">
                Aucun forum trouvé pour votre recherche
            </div>

            <!-- Mes Groupes d'activités -->
            <div v-if="filteredMyForums.length > 0" class="my-forums-section mb-6">
                <h2 class="section-title">Mes Groupes d'activités</h2>
                <ForumNavigation 
                    :current-index="currentMyForumIndex"
                    :total-forums="filteredMyForums.length"
                    @previous="previousMyForum"
                    @next="nextMyForum"
                >
                    <div class="cards-grid">
                        <div class="forum-card-container" v-for="forum in displayedMyForums" :key="forum.id">
                            <ForumCard :forum="forum" :current-user="$page.props.auth.user" />
                        </div>
                    </div>
                </ForumNavigation>
            </div>

            <!-- Autres Groupes d'activités -->
            <div v-if="filteredOtherForums.length > 0" class="other-forums-section">
                <h2 class="section-title">Autres Groupes d'activités</h2>
                <div class="cards-grid">
                    <div class="forum-card-container" v-for="forum in filteredOtherForums" :key="forum.id">
                        <OtherForumCard :forum="forum" :current-user="$page.props.auth.user" />
                    </div>
                </div>
                <PaginationComponent 
                    :currentPage="currentPage" 
                    :totalPages="totalPages" 
                    @page-changed="goToPage" 
                />
            </div>
        </main>
    </MainLayout>
</template>

<script>
import MainLayout from '../../Layouts/Main.vue';
import ForumCard from '../../components/Forum/ForumCard.vue';
import OtherForumCard from '../../components/Forum/OtherForumCard.vue';
import SearchComponent from '../../components/Forum/SearchComponent.vue';
import PaginationComponent from '../../components/Forum/PaginationComponent.vue';
import ForumNavigation from '../../Components/Forum/ForumNavigation.vue';
import { ref, onMounted, computed } from 'vue';
import '../../../css/styles/Forum.css';

export default {
    components: {
        MainLayout,
        ForumCard,
        OtherForumCard,
        SearchComponent,
        PaginationComponent,
        ForumNavigation,
    },
    props: {
        myForums: {
            type: Array,
            default: () => []
        },
        otherForums: Array,
        total: Number,
        currentPage: Number,
        lastPage: Number,
    },
    setup(props) {
        const searchTerm = ref('');
        const currentPage = ref(1);
        const forumsPerPage = 6; 
        const totalPages = ref(props.lastPage);
        const currentSort = ref('recent');
        const currentMyForumIndex = ref(0); // Add this line
        const itemsPerPage = 3; // Add this line
        const currentFilter = ref({
            query: '',
            category: '',
            sortBy: 'recent',
            hashtag: ''
        });

        const sortForums = (forums) => {
            return [...forums].sort((a, b) => {
                switch (currentFilter.value.sortBy) {
                    case 'popular':
                        return (b.views_count || 0) - (a.views_count || 0);
                    case 'comments':
                        return (b.topics?.length || 0) - (a.topics?.length || 0);
                    default: // 'recent'
                        return new Date(b.created_at) - new Date(a.created_at);
                }
            });
        };

        const filterForums = (forums, search) => {
            if (!search) return forums;
            search = search.toLowerCase();
            return forums.filter(forum => 
                forum.name.toLowerCase().includes(search) ||
                forum.description.toLowerCase().includes(search) ||
                forum.hashtags?.some(tag => tag.name.toLowerCase().includes(search))
            );
        };

        const filteredMyForums = computed(() => {
            let filtered = filterForums(props.myForums, currentFilter.value.query);
            if (currentFilter.value.category) {
                filtered = filtered.filter(forum => forum.category === currentFilter.value.category);
            }
            if (currentFilter.value.hashtag) {
                filtered = filtered.filter(forum => 
                    forum.hashtags.some(tag => tag.name === currentFilter.value.hashtag)
                );
            }
            return sortForums(filtered);
        });

        const filteredOtherForums = computed(() => {
            let filtered = filterForums(props.otherForums, currentFilter.value.query);
            if (currentFilter.value.category) {
                filtered = filtered.filter(forum => forum.category === currentFilter.value.category);
            }
            if (currentFilter.value.hashtag) {
                filtered = filtered.filter(forum => 
                    forum.hashtags.some(tag => tag.name === currentFilter.value.hashtag)
                );
            }
            return sortForums(filtered);
        });

        const showNoResults = computed(() => {
            return searchTerm.value && 
                   filteredMyForums.value.length === 0 && 
                   filteredOtherForums.value.length === 0;
        });

        const displayedMyForums = computed(() => {
            const start = currentMyForumIndex.value;
            const end = start + itemsPerPage;
            return filteredMyForums.value.slice(start, end);
        });

        const nextMyForum = () => {
            if (currentMyForumIndex.value + itemsPerPage < props.myForums.length) {
                currentMyForumIndex.value += itemsPerPage;
            }
        };

        const previousMyForum = () => {
            if (currentMyForumIndex.value > 0) {
                currentMyForumIndex.value -= itemsPerPage;
            }
        };

        const handleSearch = async (searchData) => {
            currentFilter.value = searchData;
            currentMyForumIndex.value = 0;
            currentPage.value = 1;

            try {
                const queryParams = new URLSearchParams({
                    query: searchData.query || '',
                    category: searchData.category || '',
                    sortBy: searchData.sortBy || 'recent',
                    hashtag: searchData.hashtag || '',
                    page: 1
                });

                const response = await fetch(`/forums/search?${queryParams}`);
                const data = await response.json();
                // Mise à jour des données paginées
                totalPages.value = data.lastPage;
            } catch (error) {
                console.error('Erreur lors de la recherche:', error);
            }
        };

        const handleFilter = (filters) => {
            console.log('Filters:', filters);
        };

       const goToPage = async (page) => {
            currentPage.value = page;
            try {
                const response = await fetch(`/forums/search?page=${page}&query=${searchTerm.value}`);
                const data = await response.json();
                const myForumsResults = data.forums.filter(forum => forum.user_id === props.currentUser.id);
                const otherForumsResults = data.forums.filter(forum => forum.user_id !== props.currentUser.id);
                
                filteredMyForums.value = myForumsResults;
                filteredOtherForums.value = otherForumsResults;
                totalPages.value = data.lastPage;
            } catch (error) {
                console.error('Erreur lors du changement de page:', error);
            }
        };


        onMounted(() => {
            // Initialize totalPages when the component is mounted
            totalPages.value = Math.ceil(props.total / forumsPerPage);
        });

        return {
            currentPage,
            totalPages,
            forumsPerPage,
            handleSearch,
            handleFilter,
            goToPage,
            currentMyForumIndex,
            displayedMyForums,
            nextMyForum,
            previousMyForum,
            filteredMyForums,
            filteredOtherForums,
            showNoResults,
            currentFilter
        };
    },
};
</script>

<style scoped>
.no-results {
    text-align: center;
    padding: 2rem;
    background: #f5f5f5;
    border-radius: 8px;
    margin: 1rem 0;
    color: #666;
}
</style>
