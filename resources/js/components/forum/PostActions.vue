<template>
    <div class="action-group">
        <ReactionSummary :reactions="post.reactions_count" />
        <ReactionPicker 
            v-if="post.user?.id !== currentUser?.id"
            @select="handleReaction"
            :currentReaction="post.user_reaction"
        />
        
        <button class="action-btn" @click="handleToggleComments">
            <i class="far fa-comment"></i>
            <span>{{ post.comments?.length || 0 }} Commentaires</span>
        </button>

        <button 
            v-if="canMarkBestAnswer"
            @click="handleBestAnswer"
            class="best-answer-btn"
            :class="{ 'is-best': post.is_best_answer }"
        >
            <i class="fas" :class="post.is_best_answer ? 'fa-medal' : 'fa-award'"></i>
            {{ post.is_best_answer ? 'Meilleure réponse' : 'Marquer comme meilleure réponse' }}
        </button>
    </div>
</template>

<script>
import ReactionPicker from './ReactionPicker.vue'
import ReactionSummary from './ReactionSummary.vue'

export default {
    name: 'PostActions',
    components: {
        ReactionPicker,
        ReactionSummary
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
        canMarkBestAnswer: {
            type: Boolean,
            default: false
        }
    },
    methods: {
        handleReaction(type) {
            // Émettre l'événement avec le type de réaction et le post
            this.$emit('reaction', {
                type: type,
                post: this.post
            });
        },
        handleToggleComments() {
            this.$emit('toggle-comments', this.post);
        },
        handleBestAnswer() {
            // Emettre l'événement avec le post comme paramètre
            this.$emit('toggle-best-answer', this.post);
        }
    }
}
</script>

<style scoped>
.action-group {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    border: 1px solid #ddd;
    border-radius: 20px;
    background: white;
    color: #666;
    cursor: pointer;
    transition: all 0.2s;
}

.action-btn:hover {
    border-color: #0093ee;
    color: #0093ee;
}

.best-answer-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 16px;
    border-radius: 20px;
    border: 1px solid #0093ee;
    background: transparent;
    color: #0093ee;
    cursor: pointer;
    transition: all 0.3s;
}

.best-answer-btn.is-best {
    background: #0093ee;
    color: white;
}

.best-answer-btn i {
    font-size: 1.1em;
}
</style>
