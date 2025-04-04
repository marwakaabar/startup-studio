<template>
    <div class="comments-container">
        <!-- Affichage des commentaires -->
        <div v-if="comments?.length" class="comments-list">
            <div v-for="comment in comments" :key="comment.id" class="comment-card">
                <div class="post-header" v-if="comment.user">
                    <img v-if="comment.image" :src="comment.image" alt="Avatar" class="post-author-avatar" />
                    <span v-else class="post-author-avatar-placeholder"></span>
                    <div class="post-author-info">
                        <div class="post-author-name">{{ comment.user.name }}</div>
                    </div>
                    <div class="post-date">{{ formatDate(comment.created_at) }}</div>
                </div>
                <div class="comment-content" v-if="!editingComment || editingComment !== comment.id">
                    {{ comment.content }}
                </div>
                <div v-else class="comment-edit-form">
                    <div class="edit-input-wrapper">
                        <textarea 
                            v-model="editedContent"
                            class="edit-comment-input"
                            rows="3"
                            placeholder="Modifier votre commentaire..."
                            @keydown.esc="cancelEdit"
                        ></textarea>
                    </div>
                    <div class="edit-actions">
                        <button @click="cancelEdit" class="btn-cancel">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                        <button @click="updateComment(comment.id)" class="btn-save">
                            <i class="fas fa-check"></i> Sauvegarder
                        </button>
                    </div>
                </div>
                <div class="post-footer">
                    <div class="post-reactions">
                        <div 
                            class="reaction" 
                            :class="{ 'disabled': isCommentOwner(comment) }"
                            @click="!isCommentOwner(comment) && toggleLike('comment', comment.id)"
                        >
                            <i :class="comment.is_liked ? 'fas fa-thumbs-up' : 'far fa-thumbs-up'"></i>
                            <span>{{ comment.likes_count }}</span>
                        </div>
                    </div>
                    <div class="comment-options">
                        <div class="comment-option" @click.stop="toggleOptionsMenu(comment.id)">
                            <i class="fas fa-ellipsis-h"></i>
                            <div v-if="activeMenu === comment.id" class="cmoptions-menu">
                                <div class="cmoption" @click.stop="reportComment(comment.id)">
                                    <i class="fas fa-flag"></i>
                                    Signaler
                                </div>
                                <div class="cmoption" v-if="isCommentOwner(comment)" @click.stop="showDeleteModal(comment.id)">
                                    <i class="fas fa-trash-alt"></i>
                                    Supprimer
                                </div>
                                <div class="cmoption" v-if="isCommentOwner(comment)" @click.stop="startEdit(comment)">
                                    <i class="fas fa-edit"></i>
                                    Modifier
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Formulaire de commentaire -->
        <form v-if="showCommentForm" @submit.prevent="submitComment" class="comment-form">
            <textarea 
                v-model="newComment" 
                placeholder="Ajouter un commentaire..."
                class="comment-input"
            ></textarea>
            <div class="comment-submit">
                <button type="submit" class="btn-comment">
                    <i class="fas fa-paper-plane"></i>
                    Commenter
                </button>
            </div>
        </form>
        <delete-modal 
            :show="showingDeleteModal"
            @close="closeDeleteModal"
            @confirm="confirmDelete"
        />
    </div>
</template>

<script>
import axios from 'axios';
import DeleteModal from './DeleteModal.vue';

export default {
    components: {
        DeleteModal
    },
    props: {
        postId: {
            type: Number,
            required: true
        },
        comments: {
            type: Array,
            default: () => []
        },
        showCommentForm: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            newComment: '',
            activeMenu: null,
            editingComment: null,
            editedContent: '',
            showingDeleteModal: false,
            commentToDelete: null
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

        async updateComment(commentId) {
            try {
                const response = await axios.put(`/comments/${commentId}`, {
                    content: this.editedContent
                });

                if (response.data.success) {
                    const commentIndex = this.comments.findIndex(c => c.id === commentId);
                    if (commentIndex !== -1) {
                        // Utiliser l'assignation directe au lieu de this.$set
                        this.comments[commentIndex] = response.data.comment;
                    }
                    this.editingComment = null;
                    this.editedContent = '';
                }
            } catch (error) {
                console.error('Erreur lors de la modification du commentaire:', error);
                alert(error.response?.data?.message || 'Une erreur est survenue lors de la modification');
            }
        },

        async deleteComment(commentId) {
            if (!confirm('Voulez-vous vraiment supprimer ce commentaire ?')) return;

            try {
                const response = await axios.delete(`/comments/${commentId}`);
                
                if (response.data.success) {
                    const index = this.comments.findIndex(comment => comment.id === commentId);
                    if (index !== -1) {
                        this.comments.splice(index, 1);
                    }
                    this.closeMenu();
                }
            } catch (error) {
                console.error('Erreur lors de la suppression du commentaire:', error);
                alert(error.response?.data?.message || 'Impossible de supprimer le commentaire');
            }
        },

        async toggleLike(type, id) {
            try {
                const response = await axios.post('/like', { type, id });
                const comment = this.comments.find(c => c.id === id);
                if (comment) {
                    if (response.data.message === 'Like ajouté') {
                        comment.is_liked = true;
                        comment.likes_count = response.data.likes_count;
                    } else if (response.data.message === 'Like supprimé') {
                        comment.is_liked = false;
                        comment.likes_count = response.data.likes_count;
                    }
                }
            } catch (error) {
                console.error('Erreur lors du like:', error);
            }
        },

        toggleOptionsMenu(commentId) {
            this.activeMenu = this.activeMenu === commentId ? null : commentId;
        },

        closeMenu() {
            this.activeMenu = null;
        },

        isCommentOwner(comment) {
            return comment.user_id === this.$page.props.auth.user.id;
        },

        startEdit(comment) {
            this.editingComment = comment.id;
            this.editedContent = comment.content;
            this.closeMenu();
        },

        cancelEdit() {
            this.editingComment = null;
            this.editedContent = '';
        },

        // Handling new comment submission
        async submitComment() {
            try {
                const response = await axios.post(`/posts/${this.postId}/comments`, {
                    content: this.newComment
                });

                if (response.data.success) {
                    // Ajouter le nouveau commentaire au début de la liste
                    this.comments.unshift(response.data.comment);
                    this.newComment = ''; // Reset input
                }
            } catch (error) {
                console.error('Erreur lors de l\'ajout du commentaire:', error);
                alert(error.response?.data?.message || 'Une erreur est survenue lors de l\'ajout du commentaire');
            }
        },

        showDeleteModal(commentId) {
            this.commentToDelete = commentId;
            this.showingDeleteModal = true;
            this.closeMenu();
        },

        closeDeleteModal() {
            this.showingDeleteModal = false;
            this.commentToDelete = null;
        },

        async confirmDelete() {
            if (!this.commentToDelete) return;
            
            try {
                const response = await axios.delete(`/comments/${this.commentToDelete}`);
                
                if (response.data.success) {
                    const index = this.comments.findIndex(comment => comment.id === this.commentToDelete);
                    if (index !== -1) {
                        this.comments.splice(index, 1);
                    }
                }
                this.closeDeleteModal();
            } catch (error) {
                console.error('Erreur lors de la suppression du commentaire:', error);
                alert(error.response?.data?.message || 'Impossible de supprimer le commentaire');
            }
        }
    }
};
</script>

<style scoped>
.comment-options {
  position: relative;
}

.comment-option {
  cursor: pointer;
  padding: 5px;
  position: relative;
}

.cmoptions-menu {
  position: absolute;
  right: 0;
  top: -10px;
  transform: translateY(-100%);
  background: linear-gradient(145deg, #ffffff, #f0f0f0);
  border-radius: 12px;
  box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.1),
              -5px -5px 15px rgba(255, 255, 255, 0.8);
  padding: 8px 0;
  min-width: 150px;
  z-index: 1000;
  animation: slideIn 0.2s ease-out;
  backdrop-filter: blur(5px);
}

.cmoption {
  padding: 10px 15px;
  display: flex;
  align-items: center;
  gap: 10px;
  transition: all 0.2s ease;
  color: #666;
}

.cmoption:hover {
  background: linear-gradient(145deg, #f0f0f0, #e6e6e6);
  color: #ff4757;
  transform: translateX(5px);
}

.cmoption i {
  font-size: 14px;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-90%) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(-100%) scale(1);
  }
}

.comment-edit-form {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin: 10px 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.edit-input-wrapper {
    position: relative;
    margin-bottom: 12px;
}

.edit-comment-input {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #e1e4e8;
    border-radius: 6px;
    background: white;
    font-size: 14px;
    line-height: 1.5;
    transition: all 0.2s ease;
    resize: vertical;
    min-height: 80px;
}

.edit-comment-input:focus {
    border-color: #0366d6;
    box-shadow: 0 0 0 3px rgba(3,102,214,0.1);
    outline: none;
}

.edit-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.btn-save, .btn-cancel {
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-save {
    background: #2ecc71;
    color: white;
}

.btn-save:hover {
    background: #27ae60;
}

.btn-cancel {
    background: #e74c3c;
    color: white;
}

.btn-cancel:hover {
    background: #c0392b;
}

.btn-save i, .btn-cancel i {
    font-size: 14px;
}

.reaction.disabled {
  cursor: default;
  opacity: 0.7;
}

.reaction.disabled:hover {
  background: none;
}
</style>
