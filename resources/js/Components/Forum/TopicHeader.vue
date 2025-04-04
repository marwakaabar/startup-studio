<template>
    <div class="topic-header">
        <div class="topic-header-content">
            <div class="topic-author">
                <img 
                    :src="topic.user_image" 
                    :alt="topic.user.name"
                    class="author-avatar"
                    @error="handleImageError"
                />
                <div class="author-info">
                    <span class="author-name">{{ topic.user.name }}</span>
                    <span class="post-date">{{ formatDate(topic.created_at) }}</span>
                </div>
            </div>
            
            <div class="topic-main-content">
                <h1 class="topic-title-main">{{ topic.title }}</h1>
                <div class="topic-content" v-html="topic.content"></div>
                
            </div>
            <div class="hashtags-container">
                    <span 
                        v-for="tag in hashtags" 
                        :key="tag.id" 
                        class="hashtag"
                    >
                        #{{ tag.name }}
                    </span>
                </div>
            <div class="topic-stats">
                <div class="stat-item">
                    <i class="far fa-comment"></i>
                    <span>{{ topic.posts_count }} réponses</span>
                </div>
              
                <div class="stat-item" v-if="topic.last_post">
                    <i class="far fa-clock"></i>
                    <span>Dernier message par {{ topic.last_post.user.name }}</span>
                    <span>{{ formatTimeAgo(topic.last_post.created_at) }}</span>
                </div>
            </div>

            <div class="topic-actions">
                <button @click="$emit('reply')" class="action-btn btn-primary">
                    <i class="fas fa-reply"></i> Répondre
                </button>
                <button class="action-btn btn-follow">
                    <i class="far fa-bell"></i> Suivre
                </button>
                <button @click="shareContent" class="action-btn btn-share">
                    <i class="fas fa-share-alt"></i> Partager
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        topic: {
            type: Object,
            required: true
        },
        total: {
            type: Number,
            required: true
        },
        hashtags: {
            type: Array,
            default: () => []
        }
    },
    methods: {
        handleImageError(e) {
            e.target.src = '/images/placeholder.jpg';
        },
        formatDate(date) {
            return new Date(date).toLocaleString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
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
        shareContent() {
            if (navigator.share) {
                navigator.share({
                    title: this.topic.title,
                    text: 'Découvrez cette discussion intéressante',
                    url: window.location.href
                });
            }
        }
    }
}
</script>

<style scoped>
.topic-header {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
    padding: 2rem;
}

.topic-header-content {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.topic-author {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.author-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
}

.author-info {
    display: flex;
    flex-direction: column;
}

.author-name {
    font-weight: 600;
    color: #2c3e50;
}

.post-date {
    font-size: 0.9rem;
    color: #666;
}

.topic-title-main {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 1rem;
}

.hashtags-container {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.hashtag {
    background: #e2e8f0;
    color: #4a5568;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s;
}

.hashtag:hover {
    background: #cbd5e0;
}

.topic-stats {
    display: flex;
    gap: 2rem;
    padding: 1rem 0;
    border-top: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #4a5568;
}

.topic-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.action-btn {
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
}

.btn-primary {
    background: #3182ce;
    color: white;
}

.btn-follow {
    background: #e2e8f0;
    color: #4a5568;
}

.btn-share {
    background: #e2e8f0;
    color: #4a5568;
}

.topic-content {
    line-height: 1.6;
    color: #2d3748;
}
</style>