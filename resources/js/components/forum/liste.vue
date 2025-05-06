<template>
    <div class="row">
        <div class="col-12">
            <!-- Search and filters -->
            <div class="row filters mb-4">
                <div class="col-12 d-flex align-items-center">
                    <div class="input-group me-2">
                        <span class="input-group-text">
                            <!-- Search icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                <path d="M17.8749 18L12.4244 12.3333M1.52344 7.61111C1.52344 8.47929 1.68792 9.33898 2.00748 10.1411C2.32705 10.9432 2.79544 11.672 3.38592 12.2859C3.9764 12.8998 4.6774 13.3867 5.4489 13.719C6.22039 14.0512 7.04728 14.2222 7.88234 14.2222C8.71741 14.2222 9.54429 14.0512 10.3158 13.719C11.0873 13.3867 11.7883 12.8998 12.3788 12.2859C12.9692 11.672 13.4376 10.9432 13.7572 10.1411C14.0768 9.33898 14.2412 8.47929 14.2412 7.61111C14.2412 6.74293 14.0768 5.88325 13.7572 5.08115C13.4376 4.27905 12.9692 3.55025 12.3788 2.93635C11.7883 2.32245 11.0873 1.83548 10.3158 1.50324C9.54429 1.171 8.71741 1 7.88234 1C7.04728 1 6.22039 1.171 5.4489 1.50324C4.6774 1.83548 3.9764 2.32245 3.38592 2.93635C2.79544 3.55025 2.32705 4.27905 2.00748 5.08115C1.68792 5.88325 1.52344 6.74293 1.52344 7.61111Z" stroke="#C6C6C6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <input type="text" class="form-control" v-model="searchTerm" placeholder="Chercher">
                    </div>
                    <select class="form-select" v-model="selectedPerform">
                        <option value="">filtrer</option>
                        <option v-for="per in filtrage" :key="per" :value="per">{{ per }}</option>
                    </select>
                    <select class="form-select" v-model="selectedCategory">
                        <option value="">Toutes les catégories</option>
                        <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                    </select>
                </div>
            </div>

            <!-- Mes discussions section -->
            <div class="section-header">
                <h2 class="section-title">Mes discussions</h2>
                <div class="section-actions">
                    <a href="/forums/create" class="btn btn-primary me-2">Ajouter une discussion</a>
                    <div class="recommandation-ai" @click="showAIRecommendations">
                        <b>Recommandation AI</b>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
              
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4" v-for="forum in displayedMyForums" :key="forum.id">
                    <forum-card :forum="forum" :is-owner="true"></forum-card>
                </div>
            </div>

            <!-- Autres discussions -->
            <h2 class="section-title mt-5">Autres discussions disponibles</h2>
            <div class="row mt-4">
              
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4" v-for="forum in displayedOtherForums" :key="forum.id">
                    <forum-card :forum="forum" :is-owner="false"></forum-card>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="col-12 d-flex justify-content-end mt-4">
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-md">
                    <li class="page-item prev" :class="{ disabled: currentPage === 1 }">
                        <a class="page-link" @click="changePage(currentPage - 1)" href="javascript:void(0);">
                            <i class="tf-icon ti ti-chevron-left"></i>
                        </a>
                    </li>
                    <li class="page-item" v-for="page in pages" :key="page" :class="{ active: currentPage === page }">
                        <a class="page-link" @click="changePage(page)" href="javascript:void(0);">{{ page }}</a>
                    </li>
                    <li class="page-item next" :class="{ disabled: currentPage === totalPages }">
                        <a class="page-link" @click="changePage(currentPage + 1)" href="javascript:void(0);">
                            <i class="tf-icon ti ti-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>

<script>
import ForumCard from './ForumCard.vue';
import '../../../css/Forum/listeForum.css'; 
export default {
    components: { ForumCard },
    data() {
        return {
            myForums: [],
            otherForums: [],
            searchTerm: '',
            selectedCategory: '',
            selectedPerform: '',
            categories: [],
            filtrage: ['Plus récents', 'Plus populaires', 'Plus commentés'],
            currentPage: 1,
            itemsPerPage: 6,
            loading: false,
        };
    },
    computed: {
        filteredAllForums() {
            const all = [...this.myForums, ...this.otherForums];
            return this.filterForums(all);
        },
        totalPages() {
            return Math.ceil(this.filteredAllForums.length / this.itemsPerPage);
        },
        pages() {
            return Array.from({ length: this.totalPages }, (_, i) => i + 1);
        },
        displayedMyForums() {
            const forums = this.filterForums(this.myForums);
            return forums.slice(0, this.itemsPerPage);
        },
        displayedOtherForums() {
            const forums = this.filterForums(this.otherForums);
            const remaining = this.itemsPerPage - this.displayedMyForums.length;
            return forums.slice(0, remaining);
        }
    },
    methods: {
        async loadForums() {
            this.loading = true;
            try {
                const response = await axios.get(`/api/forums`);
                this.myForums = this.processForumImages(response.data.myForums);
                this.otherForums = this.processForumImages(response.data.otherForums);
                
                // Extraire les catégories uniques des forums
                const allForums = [...this.myForums, ...this.otherForums];
                this.categories = [...new Set(allForums.map(forum => forum.category))].filter(Boolean).sort();
            } catch (error) {
                console.error("Erreur lors du chargement des forums :", error);
            } finally {
                this.loading = false;
            }
        },
        processForumImages(forums) {
            return forums.map(forum => ({
                ...forum,
                image: forum.image || `https://eu.ui-avatars.com/api/?background=0093ee&color=fff&bold=true&name=${encodeURIComponent(forum.name)}`
            }));
        },
        filterForums(forums) {
            return forums
                .filter(forum => {
                    const searchMatch = !this.searchTerm || 
                        forum.name.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                        forum.description?.toLowerCase().includes(this.searchTerm.toLowerCase());

                    const categoryMatch = !this.selectedCategory || 
                        forum.category === this.selectedCategory;

                    return searchMatch && categoryMatch;
                })
                .sort((a, b) => {
                    switch(this.selectedPerform) {
                        case 'Plus récents':
                            return new Date(b.created_at) - new Date(a.created_at);
                        case 'Plus populaires':
                            return (b.views_count || 0) - (a.views_count || 0);
                        case 'Plus commentés':
                            return (b.topics_count || 0) - (a.topics_count || 0);
                        default:
                            return 0;
                    }
                });
        },
        changePage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
            }
        },
        showAIRecommendations() {
            // log or trigger modal
            alert('Fonction AI à venir !');
        }
    },
    mounted() {
        this.loadForums();
    }
};
</script>

<style scoped>
</style>
