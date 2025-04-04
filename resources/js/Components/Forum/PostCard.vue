<template>
  <div class="post" :id="'post-' + post.id" :class="{ 'best-answer': post.is_best_answer }">
    <div class="post-header" v-if="post.user">
      <img
        v-if="post.user_image"
        :src="post.user_image"
        alt="Avatar"
        class="post-author-avatar"
        @error="handleImageError"
      />
      <span v-else class="post-author-avatar-placeholder"></span>
      <div class="post-author-info">
        <div class="post-author-name">{{ post.user.name }}</div>
      </div>
      <div class="post-date">{{ formatDate(post.created_at) }}</div>
      <div class="best-answer-badge" v-if="post.is_best_answer">
        <i class="fas fa-check-circle"></i>
        Meilleure réponse
      </div>
    </div>

    <div class="post-content" v-if="!isEditing">
      <div v-html="post.content"></div>
    </div>
    <div v-else class="post-edit-form">
      <textarea 
          v-model="editedContent"
          class="post-input"
      ></textarea>
      <div class="edit-actions">
        <button @click="updatePost" class="btn-save">
          <i class="fas fa-check"></i> Sauvegarder
        </button>
        <button @click="cancelEdit" class="btn-cancel">
          <i class="fas fa-times"></i> Annuler
        </button>
      </div>
    </div>

    <div class="post-footer">
      <div class="post-reactions">
        <div 
          class="reaction" 
          :class="{ 'disabled': isCurrentUserPost }"
          @click="!isCurrentUserPost && toggleLike('post', post.id)"
        >
          <i :class="post.is_liked ? 'fas fa-thumbs-up' : 'far fa-thumbs-up'"></i>
          <span>{{ post.likes_count }}</span>
        </div>
        <div class="reaction" @click="$emit('showComments', post.id)">
          <i class="far fa-comment"></i>
          <span>Répondre</span>
        </div>
        <button 
          v-if="canMarkBestAnswer"
          class="reaction best-answer-button"
          :class="{ 'is-best': post.is_best_answer }"
          @click="toggleBestAnswer"
        >
          <i class="fas fa-check-circle"></i>
          {{ post.is_best_answer ? 'Meilleure réponse' : 'Marquer comme meilleure réponse' }}
        </button>
      </div>
      <div class="post-options">
        <div class="post-option" @click.stop="toggleOptionsMenu(post.id)">
          <i class="fas fa-ellipsis-h"></i>
          <div
            v-if="activeMenu === post.id"
            class="options-menu"
            @click.stop
          >
            <div class="option" @click.stop="reportPost">
              <i class="fas fa-flag"></i>
              Signaler
            </div>
            <div
              class="option"
              v-if="isCurrentUserPost"
              @click.stop="showDeleteModal(post.id)"
            >
              <i class="fas fa-trash-alt"></i>
              Supprimer
            </div>
            <div class="option" v-if="isCurrentUserPost" @click.stop="startEdit">
              <i class="fas fa-edit"></i>
              Modifier
            </div>
          </div>
        </div>
      </div>
    </div>
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
    post: Object,
  },
  emits: ["showComments", "reportPost", "post-deleted", "post-updated", "best-answer-updated"],
  data() {
    return {
      activeMenu: null,
      isEditing: false,
      editedContent: '',
      showingDeleteModal: false,
      postToDelete: null
    };
  },
  computed: {
    isCurrentUserPost() {
      return this.post.user_id === this.$page.props.auth.user.id;
    },
    canMarkBestAnswer() {
      console.log('Checking canMarkBestAnswer:', {
        topicUserId: this.post.topic_user_id,
        currentUserId: this.$page.props.auth.user.id,
        isAdmin: this.$page.props.auth.user.is_admin
      });
      
      // Utiliser topic_user_id au lieu de topic.user_id
      return this.post.topic_user_id === this.$page.props.auth.user.id || 
             this.$page.props.auth.user.is_admin;
    }
  },
  mounted() {
    // Debug des données
    console.log('Post Data:', {
        post: this.post,
        topic: this.post.topic,
        currentUser: this.$page.props.auth.user,
        canMarkBestAnswer: this.canMarkBestAnswer
    });
    document.addEventListener("click", this.closeMenu);
  },
  unmounted() {
    document.removeEventListener("click", this.closeMenu);
  },
  methods: {
    formatDate(date) {
      return new Date(date).toLocaleString("fr-FR", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      });
    },
    handleImageError(event) {
      event.target.style.display = "none";
      event.target.parentElement.innerHTML =
        '<div class="post-author-avatar-placeholder"><i class="fas fa-user"></i></div>';
    },
    toggleOptionsMenu(postId) {
      this.activeMenu = this.activeMenu === postId ? null : postId;
    },
    closeMenu() {
      this.activeMenu = null;
    },
    reportPost() {
      this.$emit("reportPost", this.post.id);
      this.closeMenu();
    },
    showDeleteModal(postId) {
      this.postToDelete = postId;
      this.showingDeleteModal = true;
      this.closeMenu();
    },
    closeDeleteModal() {
      this.showingDeleteModal = false;
      this.postToDelete = null;
    },
    async confirmDelete() {
      if (!this.postToDelete) return;
      
      try {
        const response = await axios.delete(`/posts/${this.postToDelete}`);
        
        if (response.data.success) {
          this.$emit('post-deleted', this.postToDelete);
          this.closeDeleteModal();
        }
      } catch (error) {
        console.error('Erreur lors de la suppression du post:', error);
        alert(error.response?.data?.message || 'Impossible de supprimer le post');
      }
    },
    startEdit() {
      this.isEditing = true;
      this.editedContent = this.post.content;
      this.closeMenu();
    },
    cancelEdit() {
      this.isEditing = false;
      this.editedContent = '';
    },
    async updatePost() {
      try {
        const response = await axios.put(`/posts/${this.post.id}`, {
            content: this.editedContent
        });

        if (response.data.success) {
            const updatedPost = response.data.post;
            // Mettre à jour l'image en plus du contenu
            this.post.user_image = updatedPost.user_image;
            this.post.content = updatedPost.content;
            this.$emit('post-updated', this.post);
            this.isEditing = false;
        }
      } catch (error) {
        console.error('Erreur lors de la modification du post:', error);
        alert(error.response?.data?.message || 'Une erreur est survenue lors de la modification');
      }
    },
    async toggleLike(type, id) {
      try {
        const response = await axios.post('/like', { type, id });
        if (response.data.message === 'Like ajouté') {
          this.post.is_liked = true;
          this.post.likes_count = response.data.likes_count;
        } else if (response.data.message === 'Like supprimé') {
          this.post.is_liked = false;
          this.post.likes_count = response.data.likes_count;
        }
      } catch (error) {
        console.error('Erreur lors du like:', error);
      }
    },
    async toggleBestAnswer() {
      try {
        const response = await axios.post(`/posts/${this.post.id}/best-answer`);
        if (response.data.success) {
          this.$emit('best-answer-updated', {
            postId: this.post.id,
            isBestAnswer: response.data.is_best_answer
          });
          
          // Mise à jour locale du statut
          this.post.is_best_answer = response.data.is_best_answer;
        }
      } catch (error) {
        console.error('Erreur:', error);
        alert(error.response?.data?.message || 'Une erreur est survenue');
      }
    }
  },
};
</script>

<style scoped>
.post-options {
  position: relative;
}

.post-option {
  cursor: pointer;
  padding: 5px;
  position: relative;
}

.options-menu {
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
}

.option {
  padding: 10px 15px;
  display: flex;
  align-items: center;
  gap: 10px;
  transition: all 0.2s ease;
  color: #666;
}

.option:hover {
  background: linear-gradient(145deg, #f0f0f0, #e6e6e6);
  color: #ff4757;
  transform: translateX(5px);
}

.option i {
  font-size: 14px;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-90%);
  }
  to {
    opacity: 1;
    transform: translateY(-100%);
  }
}

.post-edit-form {
  margin: 10px 0;
}

.post-input {
  width: 100%;
  min-height: 100px;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ddd;
  margin-bottom: 10px;
}

.edit-actions {
  display: flex;
  gap: 10px;
}

.btn-save, .btn-cancel {
  padding: 5px 10px;
  border-radius: 5px;
  cursor: pointer;
}

.btn-save {
  background: #4CAF50;
  color: white;
}

.btn-cancel {
  background: #f44336;
  color: white;
}

.post-content {
  padding: 15px;
  word-break: break-word;
}

.post-content :deep(img) {
  max-width: 300px; /* Limite la largeur maximale */
  max-height: 300px; /* Limite la hauteur maximale */
  height: auto;
  border-radius: 8px;
  margin: 10px auto; /* Centre l'image */
  display: block;
  object-fit: contain; /* Garde les proportions de l'image */
}

.post-content :deep(a) {
  color: #2563eb;
  text-decoration: underline;
  word-break: break-all;
}

.post-content :deep(a):hover {
  color: #1d4ed8;
}

.post-content :deep(p) {
  margin-bottom: 1em;
}

.post-content :deep(ul), 
.post-content :deep(ol) {
  margin-left: 1.5em;
  margin-bottom: 1em;
}

.post-content :deep(blockquote) {
  border-left: 4px solid #e5e7eb;
  padding-left: 1em;
  margin: 1em 0;
  color: #6b7280;
}

.reaction.disabled {
  cursor: default;
  opacity: 0.7;
}

.reaction.disabled:hover {
  background: none;
}

.best-answer {
  border: 2px solid #4CAF50;
  position: relative;
  background: #f8fff8;
  border-radius: 8px;
}

.best-answer-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  background: #4CAF50;
  color: white;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.85em;
  font-weight: 600;
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.best-answer-badge i {
  font-size: 0.9em;
}

.best-answer-button {
  color: #4CAF50;
  padding: 6px 12px;
  border-radius: 8px;
  transition: all 0.3s ease;
  cursor: pointer;  
}

.best-answer-button:hover {
  opacity: 0.9;
}

.best-answer-button.is-best {
  background: #4CAF50;
  color: white;
}

.best-answer-button.can-toggle {
  cursor: pointer;
}

.best-answer-button.can-toggle:hover {
  background: #4CAF50;
  color: white;
  transform: translateY(-1px);
}

.best-answer-button.is-best {
  background: #4CAF50;
  color: white;
  font-weight: 600;
}

.best-answer-button:not(.can-toggle) {
  opacity: 1;
  cursor: default;
}
</style>
