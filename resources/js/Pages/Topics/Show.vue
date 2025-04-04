<template>
    <MainLayout :showNavbar="false" :showSidebar="true">
        <TopicHeader 
            :topic="topic"
            :total="total"
            :hashtags="hashtags"
            @reply="handleReplyClick"
        />

        <div class="post-cards">
            <div v-for="post in posts" :key="post.id" class="post-with-comments">
                <PostCard 
                :post="post" 
                @showComments="handleShowComments"
                @post-updated="handlePostUpdate"
                @post-deleted="handlePostDelete"
                @best-answer-updated="handleBestAnswerUpdate"
                />
                <CommentCard
                    v-if="postComments[post.id]?.length || selectedPostId === post.id"
                    :postId="post.id"
                    :comments="postComments[post.id]"
                    :showCommentForm="selectedPostId === post.id"
                    @submit-comment="submitComment"
                />
            </div>
        </div>

        <!-- Add Pagination Component -->
        <PaginationComponent 
            :currentPage="currentPage" 
            :totalPages="lastPage" 
            @page-changed="goToPage" 
        />

        <div class="reply-section" v-if="showReplyForm">
            <div class="reply-form-wrapper">
                <div class="reply-header">
                    <div class="reply-user">
                        <img 
                            :src="userImage" 
                            alt="User Image" 
                            class="reply-user-avatar" 
                            @error="handleImageError"
                        />
                        <span class="reply-user-name">{{ $page.props.auth.user.name }}</span>
                    </div>
                </div>
                <form @submit.prevent="submitReply" class="reply-form">
                    <div class="editor-container">
                        <QuillEditor 
                            v-model="replyContent" 
                            class="reply-textarea" 
                            placeholder="Partagez votre réponse..."
                            required
                            ref="replyTextarea"
                        ></QuillEditor>
                    </div>
                    <div class="reply-actions">
                        <button type="button" @click="showReplyForm = false" class="btn-cancel">
                            Annuler
                        </button>
                        <button type="submit" class="btn-reply">
                            <i class="fas fa-paper-plane"></i> Répondre
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </MainLayout>
</template>

<script>
import { ref, onMounted, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
import MainLayout from '../../Layouts/Main.vue';
import TopicHeader from '../../components/Forum/TopicHeader.vue';
import PostCard from '../../components/Forum/PostCard.vue';
import CommentCard from '../../components/Forum/CommentCard.vue';
import PaginationComponent from '../../components/Forum/PaginationComponent.vue';
import '../../../css/styles/Topic.css';
import QuillEditor from '../../Components/Forum/QuillEditor.vue';

export default {
    components: {
        MainLayout,
        TopicHeader,
        PostCard,
        CommentCard,
        PaginationComponent,
        QuillEditor,

    },
    props: {
        topic: Object,
        posts: Array,
        currentPage: Number,
        lastPage: Number,
        total: Number,
        hashtags: {
            type: Array,
            default: () => []
        },
    },
    setup(props) {
        const showReplyForm = ref(false);
        const replyContent = ref('');
        const userImage = ref('/images/placeholder.jpg');
        const replyTextarea = ref(null);
        const selectedPostId = ref(null);
        const comments = ref([]);
        const newComment = ref('');
        const postComments = ref({});

        const handleReplyClick = async () => {
            showReplyForm.value = true;
            await nextTick();
            replyTextarea.value.focus();
        };

        const handleImageError = (e) => {
            e.target.src = '/images/placeholder.jpg'; // Image de secours si l'image principale ne charge pas
        };

        const submitReply = () => {
            router.post(`/topics/${props.topic.id}/posts`, {
                content: replyContent.value
            }, {
                onSuccess: () => {
                    replyContent.value = '';
                    showReplyForm.value = false;
                }
            });
        };

        const goToPage = async (page) => {
            try {
                router.get(`/topics/${props.topic.id}?page=${page}`);
            } catch (error) {
                console.error('Erreur lors du changement de page:', error);
            }
        };

        const handleShowComments = (postId) => {
            selectedPostId.value = selectedPostId.value === postId ? null : postId;
        };

        const loadComments = async (postId) => {
            try {
                const response = await axios.get(`/posts/${postId}/comments`);
                postComments.value[postId] = response.data;
            } catch (error) {
                console.error('Erreur lors du chargement des commentaires:', error);
            }
        };

        const submitComment = async ({ postId, content }) => {
            if (!content.trim() || !postId) return;
            
            try {
                await axios.post(`/posts/${postId}/comments`, {
                    content: content
                });
                await loadComments(postId);
            } catch (error) {
                console.error('Erreur lors de l\'envoi du commentaire:', error);
            }
        };

        const handlePostUpdate = (updatedPost) => {
            const index = props.posts.findIndex(p => p.id === updatedPost.id);
            if (index !== -1) {
                props.posts[index] = updatedPost;
            }
        };

        const handlePostDelete = (deletedPostId) => {
            const index = props.posts.findIndex(p => p.id === deletedPostId);
            if (index !== -1) {
                props.posts.splice(index, 1);
            }
        };

        const handleBestAnswerUpdate = ({postId, isBestAnswer}) => {
            // Utiliser la référence réactive aux posts
            props.posts.forEach(post => {
                if (post.id === postId) {
                    post.is_best_answer = isBestAnswer;
                } else {
                    post.is_best_answer = false;
                }
            });
        };

        onMounted(async () => {
            const response = await fetch('/user/image');
            const data = await response.json();
            userImage.value = data.image;

            // Charger les commentaires pour chaque post
            for (const post of props.posts) {
                await loadComments(post.id);
            }
        });

        return {
            showReplyForm,
            replyContent,
            submitReply,
            goToPage,
            handleImageError,
            userImage,
            replyTextarea,
            handleReplyClick,
            selectedPostId,
            comments,
            newComment,
            handleShowComments,
            submitComment,
            postComments,
            handlePostUpdate,
            handlePostDelete,
            handleBestAnswerUpdate
        };
    },
    methods: {
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
};
</script>
<style scoped>
/* Supprimer tous les styles liés au topic-header car ils sont maintenant dans le composant TopicHeader */
</style>