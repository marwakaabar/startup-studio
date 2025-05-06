<template>
  <div v-if="show" :class="['moderation-notification', type]">
    <div class="moderation-icon">
      <i :class="icon"></i>
    </div>
    <div class="moderation-message">
      <h4>{{ title }}</h4>
      <p>{{ message }}</p>
    </div>
    <button @click="close" class="close-btn">
      <i class="fas fa-times"></i>
    </button>
  </div>
</template>

<script>
export default {
  props: {
    moderation: {
      type: Object,
      required: true
    },
    timeout: {
      type: Number,
      default: 5000 // 5 secondes par défaut
    }
  },
  data() {
    return {
      show: true,
      timer: null
    }
  },
  computed: {
    type() {
      if (this.moderation.was_modified) {
        return 'warning';
      } else if (this.moderation.was_flagged) {
        return 'info';
      } else if (this.moderation.is_toxic) {
        return 'error';
      }
      return 'info';
    },
    icon() {
      if (this.moderation.was_modified) {
        return 'fas fa-exclamation-triangle';
      } else if (this.moderation.was_flagged) {
        return 'fas fa-flag';
      } else if (this.moderation.is_toxic) {
        return 'fas fa-ban';
      }
      return 'fas fa-info-circle';
    },
    title() {
      if (this.moderation.was_modified) {
        return 'Contenu automatiquement modéré';
      } else if (this.moderation.was_flagged) {
        return 'Information';
      } else if (this.moderation.is_toxic) {
        return 'Contenu refusé';
      }
      return 'Notification';
    },
    message() {
      if (this.moderation.was_modified) {
        const reason = this.formatReason(this.moderation.reason);
        return `Votre contenu a été automatiquement modifié car il contenait des éléments inappropriés (${reason}).`;
      } else if (this.moderation.was_flagged) {
        return 'Votre contenu a été signalé pour vérification par l\'équipe de modération.';
      } else if (this.moderation.is_toxic) {
        const category = this.formatReason(this.moderation.category);
        return `Votre contenu ne peut pas être publié car il contient des propos inappropriés (${category}).`;
      }
      return 'Information sur le contenu';
    }
  },
  methods: {
    close() {
      this.show = false;
      this.$emit('close');
    },
    formatReason(reason) {
      const reasons = {
        'toxicity': 'propos toxiques',
        'severe_toxicity': 'propos très toxiques',
        'obscene': 'obscénité',
        'identity_attack': 'attaque identitaire',
        'insult': 'insulte',
        'threat': 'menace',
        'sexual_explicit': 'contenu sexuellement explicite'
      };
      
      return reasons[reason] || reason;
    }
  },
  mounted() {
    // Fermer automatiquement après le délai
    if (this.timeout > 0) {
      this.timer = setTimeout(() => {
        this.close();
      }, this.timeout);
    }
  },
  beforeUnmount() {
    // Nettoyer le timer si le composant est détruit
    if (this.timer) {
      clearTimeout(this.timer);
    }
  }
}
</script>

<style scoped>
.moderation-notification {
  display: flex;
  align-items: center;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 15px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  animation: slideIn 0.3s ease;
}

.warning {
  background-color: #fff3cd;
  border-left: 4px solid #ffc107;
}

.info {
  background-color: #d1ecf1;
  border-left: 4px solid #17a2b8;
}

.error {
  background-color: #f8d7da;
  border-left: 4px solid #dc3545;
}

.moderation-icon {
  font-size: 20px;
  margin-right: 15px;
}

.warning .moderation-icon {
  color: #f0ad4e;
}

.info .moderation-icon {
  color: #17a2b8;
}

.error .moderation-icon {
  color: #dc3545;
}

.moderation-message {
  flex: 1;
}

.moderation-message h4 {
  margin: 0 0 5px 0;
  font-size: 16px;
  font-weight: 600;
}

.moderation-message p {
  margin: 0;
  font-size: 14px;
  line-height: 1.4;
}

.close-btn {
  background: transparent;
  border: none;
  color: #6c757d;
  cursor: pointer;
  font-size: 16px;
  padding: 5px;
}

.close-btn:hover {
  color: #343a40;
}

@keyframes slideIn {
  from {
    transform: translateY(-20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}
</style> 