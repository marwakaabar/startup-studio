<template>
    <div class="post-card">
        <div class="post-header">
            <img 
                class="avatar" 
                :src="currentUser?.profile_image || fallbackImage" 
                :alt="currentUser?.name"
                @error="handleImageError"
            >
            <div class="user-info">
                <b class="user-name">{{ currentUser?.name }}</b>
                <div class="user-meta">
                    <div class="role">{{ getUserRole(currentUser) }}</div>
                </div>
            </div>
        </div>
        <div class="new-post-content">
            <textarea v-model="content" placeholder="Votre réponse..." class="post-input"></textarea>
            <div class="form-actions">
                <button class="action-button primary" @click="handleSubmit">Publier</button>
                <button class="action-button cancel" @click="$emit('cancel')">Annuler</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'NewPostForm',
    props: {
        currentUser: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            content: ''
        }
    },
    computed: {
        fallbackImage() {
            const userName = this.currentUser?.name || 'User';
            return `https://eu.ui-avatars.com/api/?background=D43347&color=fff&bold=true&name=${encodeURIComponent(userName)}`;
        }
    },
    methods: {
        getUserRole(user) {
            if (!user) return '';
            if (user.isCoach) return 'Coach';
            if (user.isStartup) return 'Startup';
            if (user.isInvestisseur) return 'Investisseur';
            if (user.isAdmin) return 'Admin';
            return 'Membre';
        },
        handleImageError(event) {
            event.target.src = this.fallbackImage;
        },
        handleSubmit() {
            this.$emit('submit', this.content);
            this.content = '';
        }
    }
}
</script>

<style scoped>
.post-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 81, 131, 0.1);
}

.post-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
}

.user-info {
    display: flex;
    flex-direction: column;
}

.user-name {
    font-size: 1.1rem;
    color: #005183;
}

.role {
    font-size: 0.9rem;
    color: #666;
}

.new-post-content {
    width: 100%;
}

.post-input {
    width: 100%;
    min-height: 120px;
    padding: 1rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-bottom: 1rem;
    resize: vertical;
    font-family: inherit;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

.action-button {
    padding: 0.5rem 1.5rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
}

.action-button.primary {
    background-color: #0093ee;
    color: white;
}

.action-button.cancel {
    background-color: #e0e0e0;
    color: #333;
}
</style>
