<template>
    <div class="post-header">
        <img class="avatar" :src="post.user_image" :alt="post.user?.name" @error="handleImageError">
        <div class="user-info">
   <div class="user-name-wrapper">
                <span class="user-name">{{ post.user?.name }}</span>
                      <!--  <span v-if="isAuthor" class="author-badge">
                    <i class="fas fa-crown"></i> Auteur
                </span>--> 
            </div>
            <div class="user-meta">
                <span class="role">{{ getUserRole(post.user) }}</span>
                <span class="join-date">inscrit depuis {{ formatDate(post.user?.created_at) }}</span>
            </div>
        </div>
        
        <div class="post-options">
            <button class="options-toggle" @click="toggleMenu">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            <div v-if="showMenu" class="options-menu">
                <template v-if="isOwnPost">
                    <button @click="$emit('edit')" class="menu-item">
                        <i class="fas fa-edit"></i> Modifier
                    </button>
                    <button @click="$emit('delete')" class="menu-item delete">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </template>
                <button v-else @click="handleReport" class="menu-item report">
                    <i class="fas fa-flag"></i> Signaler
                </button>
            </div>
        </div>
    </div>
    <ReportModal 
        v-if="showReportModal"
        :show="showReportModal"
        :type="'Post'"
        :id="post.id"
        @close="closeReportModal"
        @reported="handleReported"
    />
</template>

<script>
import ReportModal from './ReportModal.vue'

export default {
    name: 'PostHeader',
    props: {
        post: {
            type: Object,
            required: true
        },
        currentUser: {
            type: Object,
            required: true
        }
    },
    components: {
        ReportModal
    },
    data() {
        return {
            showMenu: false,
            showReportModal: false
        }
    },
    computed: {
        isOwnPost() {
            return this.currentUser?.id === this.post.user?.id;
        },
        isAuthor() {
            return this.post.user?.id === this.post.topic_user_id;
                }
    },
    methods: {
        toggleMenu() {
            this.showMenu = !this.showMenu;
        },
        handleImageError(event) {
const userName = event.target.alt || 'User';
            event.target.src = `https://eu.ui-avatars.com/api/?background=D43347&color=fff&bold=true&name=${encodeURIComponent(userName)}`;
        },
        formatDate(date) {
            if (!date) return '';
            return new Date(date).toLocaleDateString('fr-FR', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        },
        getUserRole(user) {
            if (!user) return '';
            if (user.isCoach) return 'Coach';
            if (user.isStartup) return 'Startup';
            if (user.isInvestisseur) return 'Investisseur';
            if (user.isAdmin) return 'Admin';
            return 'Membre';
        },
        handleReport() {
            this.showMenu = false;
            this.showReportModal = true;
        },
        closeReportModal() {
            this.showReportModal = false;
        },
        handleReported() {
            this.closeReportModal();
            // Optionnel : Afficher un message de succès
            alert('Post signalé avec succès');
        }
    }
}
</script>

<style scoped>
.post-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
    margin-bottom: 1rem;
}

.avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
}

.user-info {
    flex-grow: 1;
}

.user-name-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
}

.user-name {
    font-weight: 600;
    color: #005183;
}

.author-badge {
    background: #ffd700;
    color: #000;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 4px;
}

.user-meta {
    font-size: 0.9rem;
    color: #666;
}

.role {
    margin-right: 8px;
    font-weight: 500;
}

.post-options {
    position: relative;
}

.options-toggle {
    background: none;
    border: none;
    color: #666;
    cursor: pointer;
    padding: 5px;
}

.options-menu {
    position: absolute;
    right: 0;
    top: 100%;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    min-width: 150px;
    z-index: 10;
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    padding: 8px 16px;
    border: none;
    background: none;
    cursor: pointer;
    color: #333;
    text-align: left;
}

.menu-item:hover {
    background: #f5f5f5;
}

.menu-item.delete:hover {
    background: #fee;
    color: #d33;
}

.menu-item.report:hover {
    background: #fff3e0;
    color: #f90;
}
</style>
