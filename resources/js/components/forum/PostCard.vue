<template>
    <div class="post-card" :class="{ 'best-answer': post.is_best_answer }">
        <div v-if="post.is_best_answer" class="best-answer-badge">
            <i class="fas fa-medal"></i>
            <span>Meilleure réponse</span>
        </div>
        
        <PostHeader 
            :post="post" 
            :currentUser="currentUser"
            @edit="$emit('edit', post)"
            @delete="handleDelete"
            @report="$emit('report', post)"
        />

        <div v-if="!isEditing" class="post-content">
            {{ displayedContent }}
        </div>
        <PostEditForm 
            v-else 
            :initial-content="post.content"
            @save="handleUpdateStart"
            @cancel="$emit('cancel-edit')"
        />

        <PostActions 
            :post="post"
            :currentUser="currentUser"
            :can-mark-best-answer="canMarkBestAnswer"
            @reaction="handleReaction"
            @toggle-comments="handleToggleComments"
            @toggle-best-answer="handleBestAnswer"
        />

        <CommentsSection 
            v-if="post.showComments"
            :post="post"
            :currentUser="currentUser"
            @submit-comment="handleCommentSubmit"
            @edit-comment="$emit('edit-comment', $event)"
            @delete-comment="$emit('delete-comment', $event)"
        />
    </div>
</template>

<script>
import PostHeader from './PostHeader.vue';
import PostActions from './PostActions.vue';
import PostEditForm from './PostEditForm.vue';
import CommentsSection from './CommentsSection.vue';

export default {
    name: 'PostCard',
    components: {
        PostHeader,
        PostActions,
        PostEditForm,
        CommentsSection
    },
    props: {
        post: {
            type: Object,
            required: true
        },
        currentUser: {
            type: Object,
            required: true
        },
        isEditing: {
            type: Boolean,
            default: false
        },
        canMarkBestAnswer: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            optimisticContent: null
        }
    },
    computed: {
        displayedContent() {
            return this.optimisticContent || this.post.content;
        }
    },
    methods: {
        handleCommentSubmit(commentData) {
            // Cette méthode n'a plus besoin d'émettre d'événement car CommentsSection
            // gère directement l'appel API
        },
        handleUpdateStart(content) {
            if (!this.post.id) {
                console.error('Post ID is undefined');
                return;
            }
            
            this.optimisticContent = content;
            this.$emit('update', {
                id: this.post.id,
                content: content,
                onSuccess: () => {
                    this.optimisticContent = null;
                },
                onError: () => {
                    this.optimisticContent = null;
                }
            });
        },
        handleDelete() {
            if (!this.post.id) {
                console.error('Cannot delete post: ID is undefined');
                return;
            }
            this.$emit('delete', this.post);
        },
        handleBestAnswer(post) {
            if (!post || !post.id) {
                console.error('Invalid post for best answer toggle');
                return;
            }
            this.$emit('toggle-best-answer', post);
        },
        handleReaction(payload) {
            this.$emit('reaction', payload);
        },
        handleToggleComments(post) {
            if (!post || !post.id) {
                console.error('Invalid post for comments toggle');
                return;
            }
            this.$emit('toggle-comments', post);
        }
    },
    watch: {
        'post.content'() {
            this.optimisticContent = null;
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
    position: relative;
}

.post-card.best-answer {
    border: 2px solid #0093ee;
}

.best-answer-badge {
    position: absolute;
    top: -10px;
    right: 20px;
    background: #0093ee;
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 5px;
    box-shadow: 0 2px 4px rgba(0, 147, 238, 0.3);
}

.post-content {
    margin: 1rem 0;
}
</style>
