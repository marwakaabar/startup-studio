<template>
    <div class="card h-100 forum-card" :class="{ 'my-forum': isOwner }">
        <div class="visibility-badge" :class="forum.visibility === 'private' ? 'visibility-private' : 'visibility-public'">
            {{ forum.visibility === 'private' ? 'Privé' : 'Public' }}
        </div>
        
        <div class="card-header forum-image-container">
            <img v-if="forum.image" :src="forum.image" :alt="forum.name" class="forum-image">
            <div v-else class="forum-image-placeholder">
                <i class="bi bi-collection"></i>
            </div>
            <div class="forum-meta-overlay">
                <span class="badge category-badge">{{ forum.category }}</span>
            </div>
        </div>
        
        <div class="card-body p-3">
            <div class="title-container mb-2">
                <h5 class="card-title forum-title">
                    <i v-if="forum.visibility === 'private'" class="bi bi-lock-fill visibility-icon"></i>
                    {{ forum.name }}
                </h5>
                <div class="title-badge" v-if="isOwner">
                    <span class="owner-badge">Créateur</span>
                </div>
            </div>
            
            <div class="forum-meta mb-2">
                <small class="text-muted date-info">
                    <i class="bi bi-calendar3"></i> {{ formatDate(forum.created_at) }}
                </small>
            </div>
            
            <p class="card-text description mb-3">{{ forum.description }}</p>
            
            <div class="hashtags mb-3" v-if="forum.hashtags && forum.hashtags.length">
                <span v-for="hashtag in forum.hashtags.slice(0, 3)" :key="hashtag.id" class="hashtag-pill">
                    #{{ hashtag.name }}
                </span>
                <span v-if="forum.hashtags.length > 3" class="hashtag-more">+{{ forum.hashtags.length - 3 }}</span>
            </div>
            
            <div class="card-footer-content">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="author-info">
                        <img v-if="forum.user_image" 
                             :src="forum.user_image" 
                             :alt="forum.user?.name" 
                             class="author-avatar"
                             @error="handleImageError">
                        <div v-else class="author-avatar-placeholder">
                            <i class="bi bi-person"></i>
                        </div>
                        <span class="author-name">{{ forum.user?.name || 'Anonyme' }}</span>
                    </div>
                    
                    <div class="stats">
                        <span class="stat-item me-2">
                            <i class="bi bi-eye"></i> {{ formatNumber(forum.views_count) }}
                        </span>
                        <span class="stat-item me-2">
                            <i class="bi bi-chat"></i> {{ formatNumber(forum.topics_count) }}
                        </span>
                        
                        <button v-if="forum.visibility === 'private' && !isOwner && requestStatus !== 'accepted'" 
                                @click="handleParticipationRequest"
                                :class="buttonClasses"
                                class="btn btn-sm me-2">
                            <i class="bi" :class="buttonIcon"></i> {{ buttonText }}
                        </button>
                        
                        <button v-if="canAccessForum || requestStatus === 'accepted'" 
                                @click="navigateToForum"
                                class="btn btn-primary btn-sm">
                            <span>Voir</span>
                            <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import '../../../css/Forum/ForumCard.css';

export default {
    props: {
        forum: {
            type: Object,
            required: true
        },
        isOwner: {
            type: Boolean,
            default: false
        },
        currentUser: {
            type: Object,
            required: true
        }
    },
    
    data() {
        return {
            requestStatus: null
        }
    },
    
    computed: {
        canAccessForum() {
            return this.forum.visibility === 'public' || this.isOwner;
        },
        buttonText() {
            switch(this.requestStatus) {
                case 'pending': return 'En attente...';
                case 'rejected': return 'Participer';
                default: return 'Participer';
            }
        },
        buttonIcon() {
            switch(this.requestStatus) {
                case 'pending': return 'bi-clock';
                case 'accepted': return 'bi-check';
                case 'rejected': return 'bi-lock';
                default: return 'bi-lock';
            }
        },
        buttonClasses() {
            return {
                'btn': true,
                'btn-warning': this.requestStatus === 'pending',
                'btn-success': this.requestStatus === 'accepted',
                'btn-secondary': this.requestStatus === null || this.requestStatus === 'rejected'
            };
        }
    },
    
    methods: {
        formatDate(date) {
            if (!date) return 'Date inconnue';
            
            const options = {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            };
            
            return new Date(date).toLocaleDateString('fr-FR', options);
        },
        formatNumber(num) {
            if (num === undefined || num === null) return 0;
            
            if (num >= 1000) {
                return (num / 1000).toFixed(1) + 'k';
            }
            return num;
        },
        
        handleImageError(event) {
            if (this.forum.user?.name) {
                event.target.src = `https://eu.ui-avatars.com/api/?background=0093ee&color=fff&bold=true&name=${encodeURIComponent(this.forum.user.name)}`;
            }
        },
        
        async handleParticipationRequest() {
            try {
                if (this.requestStatus === 'accepted') {
                    this.navigateToForum();
                    return;
                }

                if (this.requestStatus === 'rejected' || !this.requestStatus) {
                    const response = await axios.post(`/forums/${this.forum.id}/request-participation`);
                    this.requestStatus = 'pending';
                }
            } catch (error) {
                console.error('Error requesting participation:', error);
                alert(error.response?.data?.message || 'Une erreur est survenue');
            }
        },
        
        navigateToForum() {
            window.location.href = `/forums/${this.forum.id}`;
        },
        
        async checkParticipationStatus() {
            if (this.forum.visibility === 'private' && !this.isOwner) {
                try {
                    const response = await axios.get(`/forums/${this.forum.id}/participation-status`);
                    this.requestStatus = response.data.status;
                } catch (error) {
                    console.error('Error fetching participation status:', error);
                }
            }
        }
    },
    
    mounted() {
        this.checkParticipationStatus();
    }
}
</script>
