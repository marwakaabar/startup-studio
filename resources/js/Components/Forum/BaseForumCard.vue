<template>
    <div class="discussion-card" :class="{ 'my-forum': isMyForum }">
        <span v-if="forum.visibility === 'private'" class="visibility-badge visibility-private">Privé</span>
        <span v-else class="visibility-badge visibility-public">Public</span>
        
        <div class="card-content">
            <span class="card-category">{{ forum.category }}</span>
            <h3 class="card-title">
                <a v-if="canAccessForum" @click="$inertia.visit(route('forums.show', { forum: forum.id }))">{{ forum.name }}</a>
                <span v-else>{{ forum.name }}</span>
            </h3>

            <div class="hashtags" v-if="forum.hashtags && forum.hashtags.length">
                <span v-for="(hashtag, index) in forum.hashtags" :key="index" class="hashtag">#{{ hashtag.name }}</span>
            </div>

            <p class="card-description">{{ forum.description }}</p>

            <p class="card-date">
                <i class="far fa-calendar-alt"></i>
                Créé le : {{ formatDate(forum.created_at) }}
            </p>

            <button v-if="forum.visibility === 'private' && !isForumCreator" class="join-btn">
                <i class="fas fa-lock"></i> Demander à participer
            </button>
            <button v-if="canAccessForum" @click="$inertia.visit(route('forums.show', { forum: forum.id }))" class="join-btn">
                <i class="fas fa-arrow-right"></i> Voir le Forum
            </button>

            <div class="card-meta">
                <div class="meta-stats">
                    <div class="meta-stat">
                        <i class="fas fa-comment"></i> {{ forum.topics_count }}
                    </div>
                    <div class="meta-stat">
                        <i class="fas fa-eye"></i> {{ forum.views_count || 0 }}
                    </div>
                </div>
                <div class="card-author" v-if="forum.user">
                    <img v-if="forum.user_image" :src="forum.user_image" alt="Avatar" class="author-avatar" />
                    <span v-else class="author-avatar-placeholder"></span>
                    <span class="author-name">{{ forum.user.name }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        forum: {
            type: Object,
            required: true
        },
        currentUser: {
            type: Object,
            required: true
        },
        isMyForum: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        isForumCreator() {
            return this.currentUser && this.forum.user && this.currentUser.id === this.forum.user.id;
        },
        canAccessForum() {
            return this.forum.visibility === 'public' || this.isForumCreator;
        }
    },
    methods: {
        formatDate(date) {
            return new Date(date).toLocaleDateString('fr-FR', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        }
    }
};
</script>

<style scoped>
.discussion-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    position: relative;
    transition: transform 0.2s ease;
}

.discussion-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Le reste de vos styles existants */
</style>
