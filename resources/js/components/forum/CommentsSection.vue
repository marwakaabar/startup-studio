<template>
    <div class="comments-section">
        <div v-for="comment in post.comments" :key="comment.id" class="comment">
            <div class="comment-header">
                <img class="comment-avatar" :src="comment.user_image" :alt="comment.user?.name">
                <div class="comment-info">
                    <span class="comment-author">{{ comment.user?.name }}</span>
                    <span class="comment-date">{{ formatTimeAgo(comment.created_at) }}</span>
                </div>
                
                <div class="comment-actions">
                    <template v-if="canManageComment(comment)">
                        <button @click="handleEditComment(comment)" class="comment-btn edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <div v-if="confirmingDelete === comment.id" class="delete-confirm">
                            <button @click="deleteComment(comment)" class="comment-btn confirm">
                                <i class="fas fa-check"></i>
                            </button>
                            <button @click="confirmingDelete = null" class="comment-btn cancel">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <button v-else @click="confirmingDelete = comment.id" class="comment-btn delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </template>
                    <button v-else @click="handleReportComment(comment)" class="comment-btn report">
                        <i class="fas fa-flag"></i>
                    </button>
                </div>
            </div>
            
            <div v-if="!editingCommentId || editingCommentId !== comment.id" class="comment-content">
                {{ comment.content }}
            </div>
            <div v-else class="comment-edit-form">
                <textarea 
                    v-model="editedCommentContent"
                    class="comment-input"
                    @keyup.enter.ctrl="submitEdit(comment)"
                ></textarea>
                <div class="edit-actions">
                    <button @click="submitEdit(comment)" class="edit-btn save">
                        <i class="fas fa-check"></i>
                    </button>
                    <button @click="cancelEdit" class="edit-btn cancel">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="new-comment-form">
            <textarea 
                v-model="post.newCommentContent"
                placeholder="Ajouter un commentaire..."
                class="comment-input"
                @keyup.enter.ctrl="submitComment"
            ></textarea>
            <button @click="submitComment" class="btn primary" :disabled="!post.newCommentContent?.trim()">
                <i class="fas fa-paper-plane"></i> Commenter
            </button>
        </div>
    </div>
    
    <ReportModal 
        v-if="showReportModal"
        :show="showReportModal"
        :type="'Comment'"
        :id="commentToReport?.id"
        @close="closeReportModal"
        @reported="handleReported"
    />
</template>

<script>
import DeleteModal from './DeleteModal.vue'
import ReportModal from './ReportModal.vue'
import PostEditForm from './PostEditForm.vue'
import axios from 'axios'

export default {
    name: 'CommentsSection',
    components: {
        ReportModal
    },
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
    data() {
        return {
            newComment: '',
            editingCommentId: null,
            editedCommentContent: '',
            showDeleteModal: false,
            commentToDelete: null,
            confirmingDelete: null,
            showReportModal: false,
            commentToReport: null
        }
    },
    methods: {
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
        async submitComment() {
            if (!this.post.newCommentContent?.trim()) return;
            
            try {
                const response = await axios.post(`/posts/${this.post.id}/comments`, {
                    content: this.post.newCommentContent
                });

                if (response.data.success) {
                    // Ajouter le nouveau commentaire à la liste
                    this.post.comments.unshift(response.data.comment);
                    // Vider le champ de commentaire
                    this.post.newCommentContent = '';
                }
            } catch (error) {
                console.error('Error submitting comment:', error.response?.data?.message || error.message);
                alert(error.response?.data?.message || 'Erreur lors de l\'envoi du commentaire');
            }
        },
        canManageComment(comment) {
            return comment.user?.id === this.currentUser?.id || this.currentUser?.isAdmin;
        },
        handleEditComment(comment) {
            this.editingCommentId = comment.id;
            this.editedCommentContent = comment.content;
        },
        async deleteComment(comment) {
            try {
                const response = await axios.delete(`/comments/${comment.id}`);
                if (response.data.success) {
                    const index = this.post.comments.findIndex(c => c.id === comment.id);
                    if (index !== -1) {
                        this.post.comments.splice(index, 1);
                    }
                }
            } catch (error) {
                console.error('Error deleting comment:', error);
                alert(error.response?.data?.message || 'Erreur lors de la suppression du commentaire');
            } finally {
                this.confirmingDelete = null;
            }
        },
        handleReportComment(comment) {
            this.commentToReport = comment;
            this.showReportModal = true;
        },
        closeReportModal() {
            this.showReportModal = false;
            this.commentToReport = null;
        },
        handleReported(data) {
            this.closeReportModal();
        },
        async submitEdit(comment) {
            if (!this.editedCommentContent.trim()) return;
            
            try {
                const response = await axios.put(`/comments/${comment.id}`, {
                    content: this.editedCommentContent
                });

                if (response.data.success) {
                    // Mettre à jour le commentaire dans le state local
                    const index = this.post.comments.findIndex(c => c.id === comment.id);
                    if (index !== -1) {
                        this.post.comments[index] = response.data.comment;
                    }
                    this.cancelEdit();
                }
            } catch (error) {
                console.error('Error updating comment:', error.response?.data?.message || error.message);
                alert(error.response?.data?.message || 'Erreur lors de la mise à jour du commentaire');
            }
        },
        cancelEdit() {
            this.editingCommentId = null;
            this.editedCommentContent = '';
        }
    }
}
</script>

<style scoped>
.comments-section {
    margin-top: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 12px;
}

.comment {
    background: white;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.comment-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 0.5rem;
}

.comment-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
}

.comment-info {
    flex-grow: 1;
}

.comment-author {
    font-weight: 500;
    color: #005183;
    margin-right: 8px;
}

.comment-date {
    color: #666;
    font-size: 0.9rem;
}

.comment-actions {
    display: flex;
    gap: 5px;
}

.comment-btn {
    padding: 4px 8px;
    border: none;
    background: none;
    color: #666;
    cursor: pointer;
    transition: color 0.2s;
}

.comment-btn:hover {
    color: #0093ee;
}

.comment-btn.delete:hover {
    color: #dc3545;
}

.comment-btn.report:hover {
    color: #f90;
}

.comment-btn.edit:hover {
    color: #0093ee;
}

.comment-content {
    color: #333;
    line-height: 1.4;
}

.comment-edit-form {
    margin-top: 0.5rem;
}

.comment-edit-form textarea {
    width: 100%;
    padding: 0.5rem;
    border: 2px solid #0093ee;
    border-radius: 6px;
    resize: vertical;
    min-height: 60px;
    margin-bottom: 0.5rem;
}

.edit-actions {
    display: flex;
    gap: 8px;
    justify-content: flex-end;
}

.edit-btn {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
}

.edit-btn.save {
    background: #28a745;
    color: white;
}

.edit-btn.save:hover {
    background: #218838;
}

.edit-btn.cancel {
    background: #dc3545;
    color: white;
}

.edit-btn.cancel:hover {
    background: #c82333;
}

.new-comment-form {
    margin-top: 1rem;
    display: flex;
    gap: 10px;
    align-items: flex-start;
}

.comment-input {
    flex-grow: 1;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    resize: vertical;
    min-height: 40px;
    font-family: inherit;
}

.comment-input:focus {
    outline: none;
    border-color: #0093ee;
    box-shadow: 0 0 0 2px rgba(0, 147, 238, 0.1);
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 5px;
}

.btn.primary {
    background: #0093ee;
    color: white;
}

.btn.primary:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.delete-confirm {
    display: inline-flex;
    gap: 4px;
}

.comment-btn.confirm {
    color: #28a745;
}

.comment-btn.confirm:hover {
    color: #218838;
}

.comment-btn.cancel {
    color: #dc3545;
}

.comment-btn.cancel:hover {
    color: #c82333;
}
</style>
