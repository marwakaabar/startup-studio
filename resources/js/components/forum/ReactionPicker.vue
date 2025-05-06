<template>
  <div class="reaction-picker" v-click-outside="close">
    <button @click.stop="toggleReaction('like')" class="reaction-button" @mouseenter="showPicker" @mouseleave="hidePicker"
      :class="{ 'has-reaction': currentReaction }">
      <span :style="{ color: getReactionColor(currentReaction) }">
        {{ currentReaction ? getReactionEmoji(currentReaction) : '👍' }}
        <span class="reaction-text">
          {{ currentReaction ? getReactionLabel(currentReaction) : "J'aime" }}
        </span>
      </span>
    </button>
    <div v-show="isOpen" class="reaction-popup" @mouseleave="hidePicker" @mouseenter="keepOpen">
      <button 
        v-for="reaction in reactions" 
        :key="reaction.type"
        class="reaction-item"
        @click.stop="toggleReaction(reaction.type)"
        :title="reaction.label"
        @mouseenter="showLabel(reaction)"
      >
        {{ reaction.emoji }}
        <span class="reaction-tooltip">{{ reaction.label }}</span>
      </button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      isOpen: false,
      hoverTimeout: null,
      reactions: [
        { type: 'like', emoji: '👍', label: "J'aime" },
        { type: 'love', emoji: '❤️', label: "J'adore" },
        { type: 'haha', emoji: '😂', label: 'Haha' },
        { type: 'wow', emoji: '😮', label: 'Wow' },
        { type: 'sad', emoji: '😢', label: 'Triste' },
        { type: 'angry', emoji: '😠', label: 'Grr' }
      ]
    }
  },
  props: {
    currentReaction: {
      type: String,
      default: null
    }
  },
  methods: {
    togglePicker() {
      this.isOpen = !this.isOpen;
    },
    toggleReaction(type) {
      // Si c'est la même réaction, on la retire
      if (this.currentReaction === type) {
        this.$emit('select', null);
      } else {
        // Sinon on applique la nouvelle réaction
        this.$emit('select', type);
      }
      this.close();
    },
    close() {
      this.isOpen = false;
    },
    showPicker() {
      clearTimeout(this.hoverTimeout);
      this.isOpen = true;
    },
    hidePicker() {
      this.hoverTimeout = setTimeout(() => {
        this.isOpen = false;
      }, 300);
    },
    keepOpen() {
      clearTimeout(this.hoverTimeout);
    },
    getReactionEmoji(type) {
      const reaction = this.reactions.find(r => r.type === type);
      return reaction ? reaction.emoji : '';
    },
    getReactionLabel(type) {
      const reaction = this.reactions.find(r => r.type === type);
      return reaction ? reaction.label : '';
    },
    showLabel(reaction) {
      // Placeholder for any additional logic to show label
    },
    getReactionColor(type) {
      const colors = {
        like: '#666',
        love: '#e74c3c',
        haha: '#f1c40f',
        wow: '#f39c12',
        sad: '#3498db',
        angry: '#e74c3c'
      };
      return colors[type] || '#666';
    }
  },
  directives: {
    'click-outside': {
      mounted(el, binding) {
        el.clickOutsideEvent = (event) => {
          if (!(el === event.target || el.contains(event.target))) {
            binding.value(event);
          }
        };
        document.addEventListener('click', el.clickOutsideEvent);
      },
      unmounted(el) {
        document.removeEventListener('click', el.clickOutsideEvent);
      },
    },
  }
}
</script>

<style scoped>
.reaction-picker {
  position: relative;
  display: inline-flex;
  align-items: center;
}

.reaction-button {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border: none;
  background: transparent;
  color: #65676b;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  border-radius: 6px;
  transition: background-color 0.2s;
}

.reaction-button:hover {
  background-color: #f0f2f5;
}

.reaction-button.has-reaction {
  font-weight: bold;
}

.reaction-button.has-reaction:hover {
  filter: brightness(1.1);
}

.reaction-button span {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  transition: all 0.2s ease;
}

.reaction-text {
  margin-left: 4px;
}

.reaction-popup {
  position: absolute;
  bottom: 100%;
  left: -10px;
  background: white;
  border-radius: 28px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  padding: 8px;
  display: flex;
  gap: 2px;
  margin-bottom: 10px;
  z-index: 1000;
  animation: slideUp 0.2s ease-out;
}

.reaction-item {
  background: none;
  border: none;
  padding: 8px;
  font-size: 1.8rem;
  cursor: pointer;
  transition: transform 0.2s;
  position: relative;
  border-radius: 50%;
}

.reaction-item:hover {
  transform: scale(1.3) translateY(-5px);
  z-index: 1;
}

.reaction-tooltip {
  position: absolute;
  top: -25px;
  left: 50%;
  transform: translateX(-50%);
  background: rgba(0, 0, 0, 0.8);
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  white-space: nowrap;
  opacity: 0;
  transition: opacity 0.2s;
  pointer-events: none;
}

.reaction-item:hover .reaction-tooltip {
  opacity: 1;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
