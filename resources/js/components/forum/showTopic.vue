<template>
    <div class="forum">
        <b class="topic-title">{{ topic.title }}</b>
        <div class="topic-description">{{ topic.content }}</div>
        
        <TopicMetadata :topic="topic" :posts="posts" />

        <div class="action-buttons">
            <div class="button-wrapper" @click="showNewPostForm">
                <b class="button-text">Répondre</b>
            </div>
            <div class="button-wrapper" @click="toggleSubscription">
                <b class="button-text">{{ isFollowing ? 'Abonné' : 'Suivre' }}</b>
            </div>
        </div>

        <NewPostForm 
            v-if="showingNewPostForm"
            :currentUser="currentUser"
            @submit="submitPost"
            @cancel="cancelNewPost"
        />

        <div class="posts-container">
            <PostCard
                v-for="post in posts"
                :key="post.id"
                :post="post"
                :current-user="currentUser"
                :is-editing="editingPostId === post.id"
                :can-mark-best-answer="canMarkAsBestAnswer(post)"
                @edit="startEdit"
                @delete="showDeleteConfirm"
                @report="showReportModal"
                @update="updatePost"
                @cancel-edit="cancelEdit"
                @reaction="handleReaction"
                @toggle-comments="toggleComments"
                @toggle-best-answer="toggleBestAnswer"
                @submit-comment="submitComment"
            />
        </div>

        <DeleteModal 
            v-if="showDeleteModal"
            :show="showDeleteModal"
            @close="showDeleteModal = false"
            @confirm="confirmDelete"
        />
        
        <ReportModal
            v-if="showReportModal"
            :show="showReportModal"
            :type="reportType"
            :id="reportId"
            @close="closeReportModal"
            @reported="handleReported"
        />
    </div>
</template>

<script>
import axios from 'axios';
import TopicMetadata from './TopicMetadata.vue';
import PostCard from './PostCard.vue';
import NewPostForm from './NewPostForm.vue';
import DeleteModal from './DeleteModal.vue';
import ReportModal from './ReportModal.vue';

export default {
    components: {
        TopicMetadata,
        PostCard,
        NewPostForm,
        DeleteModal,
        ReportModal
    },
    props: {
        topicId: {
            type: [Number, String],
            required: true
        },
        initialTopic: {
            type: Object,
            required: true
        },
        initialPosts: {
            type: Array,
            required: true
        },
        currentUser: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            topic: this.initialTopic,
            posts: this.initialPosts.map(post => ({
                ...post,
                showComments: false,
                comments: post.comments || [],
                reactions_count: post.reactions_count || {},
                user_reaction: post.user_reaction || null,
                newCommentContent: ''
            })),
            isFollowing: false,
            showingNewPostForm: false,
            newPostContent: '',
            loading: false,
            activeMenu: null,
            editingPostId: null,
            editedContent: '',
            showDeleteModal: false,
            postToDelete: null,
            showReportModal: false,
            reportType: null,
            reportId: null,
            postModerationInfo: {}
        }
    },
    computed: {
        isCurrentUserPost() {
            return this.currentUser && this.topic.user_id === this.currentUser.id;
        },
        canMarkAsBestAnswer() {
            return post => {
                return (this.currentUser?.id === this.topic.user_id) || 
                       (this.currentUser?.isAdmin === true);
            };
        }
    },
    methods: {
        userRole() {
        if (this.currentUser.isCoach) return 'Coach';
        if (this.currentUser.isStartup) return 'Startup';
        if (this.currentUser.isInvestisseur) return 'Investisseur';
        if (this.currentUser.isAdmin) return 'Admin';
        return 'Membre';
    },
    userImage() {
        // Assurez-vous que l'image est correctement définie
        return this.currentUser.profile_image || 'https://eu.ui-avatars.com/api/?background=D43347&color=fff&bold=true&name=' + encodeURIComponent(this.currentUser.name);
    },
        async loadTopicData() {
            try {
                const response = await axios.get(`/topics/${this.topicId}`);
                this.topic = response.data.topic;
                
                // S'assurer que chaque post a un ID valide
                this.posts = response.data.posts.map(post => ({
                    ...post,
                    id: post.id, // S'assurer que l'ID est explicitement copié
                    showComments: false,
                    comments: post.comments || [],
                    reactions_count: post.reactions_count || {},
                    user_reaction: post.user_reaction || null,
                    newCommentContent: '',
                    user: post.user || null
                }));
                this.isFollowing = response.data.isFollowing;

                if (!this.topic.user) this.topic.user = null;
                if (!this.topic.last_post) this.topic.last_post = { user: null };

                const subscriptionResponse = await axios.get(`/topics/${this.topicId}/subscription-status`);
                this.isFollowing = subscriptionResponse.data.subscribed;
            } catch (error) {
                console.error('Error loading topic:', error);
            }
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
        async toggleBestAnswer(post) {
            if (!post || !post.id) {
                console.error('Invalid post for best answer toggle');
                return;
            }

            try {
                // Ajout des logs pour le débogage
                console.log('Toggling best answer for post:', post.id);
                
                const response = await axios.post(`/posts/${post.id}/best-answer`);
                console.log('Best answer response:', response.data);
                
                if (response.data.success) {
                    // Mettre à jour tous les posts
                    this.posts = this.posts.map(p => ({
                        ...p,
                        is_best_answer: p.id === post.id ? response.data.is_best_answer : false
                    }));
                }
            } catch (error) {
                console.error('Error toggling best answer:', error);
                // En cas d'erreur, recharger les données
                await this.loadTopicData();
            }
        },
        showNewPostForm() {
            this.showingNewPostForm = true;
        },
        cancelNewPost() {
            this.showingNewPostForm = false;
            this.newPostContent = '';
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
        async submitPost(content) {
            try {
                const response = await axios.post(`/topics/${this.topicId}/posts`, {
                    content: content
                });
                
                if (response.data.success) {
                    this.posts.unshift(response.data.post);
                    this.cancelNewPost();
                }
            } catch (error) {
                console.error('Error submitting post:', error);
            }
        },
        async toggleSubscription() {
            try {
                const response = await axios.post(`/topics/${this.topicId}/subscribe`);
                this.isFollowing = response.data.subscribed;
            } catch (error) {
                console.error('Error toggling subscription:', error);
            }
        },
        async handleReaction({ type, post }) {
            if (!post || !post.id || !this.currentUser) return;
            
            try {
                const postIndex = this.posts.findIndex(p => p.id === post.id);
                if (postIndex !== -1) {
                    const updatedPost = { ...this.posts[postIndex] };
                    const oldReaction = updatedPost.user_reaction;
                    const currentReactions = { ...updatedPost.reactions_count };

                    // Mise à jour optimiste
                    if (oldReaction) {
                        currentReactions[oldReaction] = Math.max(0, (currentReactions[oldReaction] || 1) - 1);
                    }
                    if (type) {
                        currentReactions[type] = (currentReactions[type] || 0) + 1;
                    }
                    
                    updatedPost.reactions_count = currentReactions;
                    updatedPost.user_reaction = type;
                    
                    // Mettre à jour le post entier
                    this.posts.splice(postIndex, 1, updatedPost);
                }

                const response = await axios.post('/react', {
                    reactable_type: 'Post',
                    reactable_id: post.id,
                    type: type,
                    reaction: type
                });

                if (response.data.success && postIndex !== -1) {
                    // Mettre à jour avec les données du serveur
                    const updatedPost = { ...this.posts[postIndex] };
                    updatedPost.reactions_count = response.data.reactions;
                    this.posts.splice(postIndex, 1, updatedPost);
                }
            } catch (error) {
                console.error('Error reacting:', error);
                await this.loadTopicData(); // Recharger en cas d'erreur
            }
        },
        async updatePost({ id, content, onSuccess, onError }) {
            if (!id) {
                console.error('Cannot update post: ID is undefined');
                onError && onError();
                return;
            }

            try {
                const response = await axios.put(`/posts/${id}`, {
                    content: content
                });

                if (response.data.success) {
                    const postIndex = this.posts.findIndex(p => p.id === id);
                    if (postIndex !== -1) {
                        const updatedPost = {
                            ...this.posts[postIndex],
                            content: response.data.post.content,
                            id: response.data.post.id // S'assurer que l'ID est préservé
                        };

                        // Préserver les propriétés existantes
                        ['showComments', 'comments', 'reactions_count', 'user_reaction'].forEach(prop => {
                            if (this.posts[postIndex][prop] !== undefined) {
                                updatedPost[prop] = this.posts[postIndex][prop];
                            }
                        });

                        this.posts.splice(postIndex, 1, updatedPost);
                    }

                    this.cancelEdit();
                    onSuccess && onSuccess();
                }
            } catch (error) {
                console.error('Error updating post:', error);
                onError && onError();
            }
        },
        async toggleComments(post) {
            if (!post || !post.id) return;
            
            try {
                const postIndex = this.posts.findIndex(p => p.id === post.id);
                if (postIndex !== -1) {
                    const updatedPost = { ...this.posts[postIndex] };
                    updatedPost.showComments = !updatedPost.showComments;
                    
                    // Charger les commentaires si nécessaire
                    if (updatedPost.showComments && (!updatedPost.comments || !updatedPost.comments.length)) {
                        console.log('Loading comments for post:', post.id);
                        const response = await axios.get(`/posts/${post.id}/comments`);
                        updatedPost.comments = response.data || [];
                    }
                    
                    // Mettre à jour le post
                    this.posts.splice(postIndex, 1, updatedPost);
                }
            } catch (error) {
                console.error('Error toggling comments:', error);
            }
        },
        async loadComments(post) {
            if (!post || !post.id) return;

            try {
                console.log('Loading comments for post:', post.id);
                const response = await axios.get(`/posts/${post.id}/comments`);
                
                const postIndex = this.posts.findIndex(p => p.id === post.id);
                if (postIndex !== -1) {
                    const updatedPost = { ...this.posts[postIndex] };
                    updatedPost.comments = response.data || [];
                    this.posts.splice(postIndex, 1, updatedPost);
                }
            } catch (error) {
                console.error('Error loading comments:', error);
            }
        },
        async submitComment(post) {
            if (!post || !post.newCommentContent?.trim()) return;

            try {
                const response = await axios.post(`/posts/${post.id}/comments`, {
                    content: post.newCommentContent
                });
                
                if (response.data.success) {
                    const postIndex = this.posts.findIndex(p => p.id === post.id);
                    if (postIndex !== -1) {
                        // Créer une copie du post
                        const updatedPost = { ...this.posts[postIndex] };
                        
                        // S'assurer que comments est un tableau
                        if (!Array.isArray(updatedPost.comments)) {
                            updatedPost.comments = [];
                        }
                        
                        // Ajouter le nouveau commentaire
                        updatedPost.comments.unshift(response.data.comment);
                        
                        // Vider le contenu du nouveau commentaire
                        updatedPost.newCommentContent = '';
                        
                        // Mettre à jour le post dans le tableau
                        this.posts.splice(postIndex, 1, updatedPost);
                    }
                }
            } catch (error) {
                console.error('Error submitting comment:', error);
            }
        },
        async handleEditComment({ postId, commentId, content }) {
            try {
                const response = await axios.put(`/comments/${commentId}`, { content });
                
                if (response.data.success) {
                    const postIndex = this.posts.findIndex(p => p.id === postId);
                    if (postIndex !== -1) {
                        const updatedPost = { ...this.posts[postIndex] };
                        const commentIndex = updatedPost.comments.findIndex(c => c.id === commentId);
                        
                        if (commentIndex !== -1) {
                            updatedPost.comments[commentIndex] = response.data.comment;
                            this.posts.splice(postIndex, 1, updatedPost);
                        }
                    }
                }
            } catch (error) {
                console.error('Error updating comment:', error);
                // Recharger les commentaires en cas d'erreur
                await this.loadComments(this.posts.find(p => p.id === postId));
            }
        },
        async handleDeleteComment({ postId, commentId }) {
            try {
                // Mise à jour optimiste
                const postIndex = this.posts.findIndex(p => p.id === postId);
                if (postIndex !== -1) {
                    const updatedPost = { ...this.posts[postIndex] };
                    updatedPost.comments = updatedPost.comments.filter(c => c.id !== commentId);
                    this.posts.splice(postIndex, 1, updatedPost);
                }

                const response = await axios.delete(`/comments/${commentId}`);
                if (!response.data.success) {
                    // Rollback en cas d'échec
                    await this.loadComments(this.posts[postIndex]);
                }
            } catch (error) {
                console.error('Error deleting comment:', error);
                // Recharger les commentaires en cas d'erreur
                const post = this.posts.find(p => p.id === postId);
                if (post) {
                    await this.loadComments(post);
                }
            }
        },
        startEdit(post) {
            this.editingPostId = post.id;
            this.editedContent = post.content;
            this.activeMenu = null;
        },
        cancelEdit() {
            this.editingPostId = null;
            this.editedContent = '';
        },
        async confirmDelete() {
            if (!this.postToDelete || !this.postToDelete.id) {
                console.error('No post selected for deletion');
                return;
            }

            try {
                const response = await axios.delete(`/posts/${this.postToDelete.id}`);
                
                if (response.data.success) {
                    // Suppression optimiste du post
                    const postIndex = this.posts.findIndex(p => p.id === this.postToDelete.id);
                    if (postIndex !== -1) {
                        this.posts.splice(postIndex, 1);
                    }
                    
                    // Réinitialiser l'état
                    this.showDeleteModal = false;
                    this.postToDelete = null;
                }
            } catch (error) {
                console.error('Error deleting post:', error);
                // En cas d'erreur, recharger les données
                this.loadTopicData();
            }
        },
        showDeleteConfirm(post) {
            if (!post || !post.id) {
                console.error('Invalid post for deletion');
                return;
            }
            this.postToDelete = post;
            this.showDeleteModal = true;
            this.activeMenu = null;
        },
        showReportModal(post) {
            this.reportType = 'Post';
            this.reportId = post.id;
            this.showReportModal = true;
            this.activeMenu = null;
        },
        closeReportModal() {
            this.showReportModal = false;
            this.reportType = null;
            this.reportId = null;
        }
    },
    mounted() {
        this.loadTopicData();
    }
};
</script>

<style scoped>
.forum {
    padding: 2rem;
    background-color: #f1f9ff;
    min-height: 100vh;
}

.topic-title {
    font-size: 25px;
    color: #0093ee;
    margin-bottom: 1rem;
}

.topic-description {
    margin-bottom: 2rem;
    color: #333;
    line-height: 1.5;
}

.action-buttons {
    display: flex;
    gap: 20px;
    margin-bottom: 2rem;
}

.button-wrapper {
    background-color: #0093ee;
    border-radius: 8px;
    padding: 16px 24px;
    color: white;
    cursor: pointer;
    width: 194px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.button-wrapper.subscribed {
    background-color: #005183;
}
</style>
