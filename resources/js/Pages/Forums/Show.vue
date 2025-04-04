<template>
    <MainLayout :showNavbar="false" :showSidebar="true">
      <main class="main-content">
    

    <div class="page-header">
        <h1 class="page-title">Détails du Forum </h1>
        <Link 
            v-if="$page.props.auth.user.role !== 'investisseur'"
            :href="`/forums/${forum.id}/topics/create`" 
            class="new-topic-btn"
        >
            <i class="fas fa-plus"></i> Ajouter un Sujet
        </Link>
    </div>

    <!-- Informations sur le forum -->
    <div class="forum-info-container">
        <div class="forum-image-header" v-if="forum.image">
            <img :src="forum.image" :alt="forum.name" class="forum-cover-image"/>
        </div>
        <div class="forum-content">
            <h1 class="forum-name">{{ forum.name }}</h1>
            <div class="forum-visibility">
                <i class="fas" :class="forum.visibility === 'private' ? 'fa-lock' : 'fa-globe'"></i>
                <span>{{ forum.visibility === 'private' ? 'Privé' : 'Public' }}</span>
            </div>
            <div class="forum-description">
                <p>{{ forum.description }}</p>
            </div>
        </div>
    </div>

    <!-- Forum Container -->
    <div class="forum-container">
        <div class="forum-tabs">
            <div class="forum-tab active">Tous les sujets</div>
           <!-- <div class="forum-tab">Récents</div>
            <div class="forum-tab">Populaires</div>
            <div class="forum-tab">Sans réponse</div>-->
        </div>

        <table class="topics-table">
            <thead>
                <tr>
                    <th style="width: 50%">Sujet</th>
                    <th style="width: 15%">Auteur</th>
                    <th style="width: 10%" class="topic-stats">Réponses</th>
                    <th style="width: 10%" class="topic-stats">Vues</th>
                    <th style="width: 15%" class="topic-last-post">Dernier message</th>
                </tr>
            </thead>
            <tbody>
                <!-- Boucle sur les sujets -->
                <template v-for="topic in topics" :key="topic.id">
                    <tr>
                        <td>
                            <div class="topic-title-cell">
                                <div class="topic-icon icon-discussion">
                                    <i class="fas fa-comments"></i>
                                </div>
                                <div class="topic-info">
                                    <div class="topic-title">
                                        <a :href="`/topics/${topic.id}`">{{ topic.title }}</a>
                                        <span class="topic-badge" v-if="topic.isHot">Populaire</span>
                                    </div>
                                    <div class="topic-meta">
                                       <span><i class="far fa-calendar-alt"></i> {{ formatDate(topic.created_at) }}</span>
                                        <!--     <span><i class="far fa-folder"></i> {{ topic.category }}</span>-->
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="user-cell">
                             <!--   <img :src="topic.author.avatar" alt="Avatar" class="user-avatar">-->
                                <span class="user-name">{{ topic.user.name }}</span>
                            </div>
                        </td>
                        <td class="stats-cell topic-stats">
                            <div class="stats-number">{{ topic.posts_count }} </div>
                            <div class="stats-label">Réponses</div>
                        </td>
                        <td class="stats-cell topic-stats">
                            <div class="stats-number">{{ topic.views_count }}</div>
                            <div class="stats-label">Vues</div>
                        </td>
                        <td class="topic-last-post">
                            <div class="last-reply">
                                <div>{{ formatTimeAgo(topic.last_post.created_at) }}</div>
                                <div>par <strong>{{ topic.last_post.user.name }}</strong></div>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <!-- Add Pagination Component -->
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
import TopicCard from '../../components/TopicCard.vue';
import PaginationComponent from '../../components/Forum/PaginationComponent.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import '../../../css/styles/Sujet.css';

export default {
    components: {
        MainLayout,
        TopicCard,
        Link,
        PaginationComponent,
    },
    props: {
        forum: Object,
        topics: Array,
        currentPage: Number,
        lastPage: Number,
        total: Number,
    },
    setup(props) {
        const currentPage = ref(props.currentPage);
        const totalPages = ref(props.lastPage);

        const goToPage = async (page) => {
            currentPage.value = page;
            try {
                const response = await fetch(`/forums/${props.forum.id}?page=${page}`);
                const data = await response.json();
                topics.value = data.topics;
                totalPages.value = data.lastPage;
            } catch (error) {
                console.error('Erreur lors du changement de page:', error);
            }
        };

        return {
            goToPage,
            currentPage,
            totalPages
        };
    },
    methods: {
        formatDate(date) {
            return new Date(date).toLocaleDateString('fr-FR', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        },

        formatTimeAgo(date) {
            const now = new Date();
            const past = new Date(date);
            const diff = now - past;
            
            const minutes = Math.floor(diff / 60000);
            const hours = Math.floor(minutes / 60);
            const days = Math.floor(hours / 24);

            if (days > 0) return `il y a ${days} jour${days > 1 ? 's' : ''}`;
            if (hours > 0) return `il y a ${hours} heure${hours > 1 ? 's' : ''}`;
            if (minutes > 0) return `il y a ${minutes} minute${minutes > 1 ? 's' : ''}`;
            return 'à l\'instant';
        },
    }
};
</script>
