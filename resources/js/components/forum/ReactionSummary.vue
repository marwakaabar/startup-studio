<template>
  <div class="reactions-summary" v-if="hasReactions">
    <div class="reactions-group" @mouseover="showTooltip" @mouseleave="hideTooltip">
      <div v-for="reaction in sortedReactions" 
           :key="reaction.type" 
           class="reaction-item">
        <span class="reaction-emoji">{{ getReactionEmoji(reaction.type) }}</span>
        <span class="reaction-count">{{ reaction.count }}</span>
      </div>
    </div>
    <!-- Tooltip détaillé -->
    <div v-if="showDetail" class="reactions-tooltip">
      <div v-for="reaction in sortedReactions" 
           :key="reaction.type" 
           class="tooltip-item">
        <span>{{ getReactionEmoji(reaction.type) }}</span>
        <span>{{ reaction.count }} {{ getReactionLabel(reaction.type) }}</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    reactions: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      showDetail: false
    };
  },
  computed: {
    sortedReactions() {
      // Regrouper toutes les réactions par type
      return Object.entries(this.reactions)
        .sort(([, a], [, b]) => b - a)
        .map(([type, count]) => ({
          type,
          count
        }));
    },
    hasReactions() {
      return Object.keys(this.reactions).length > 0;
    }
  },
  methods: {
    getReactionEmoji(type) {
      const emojis = {
        like: '👍',
        love: '❤️',
        haha: '😂',
        wow: '😮',
        sad: '😢',
        angry: '😠'
      };
      return emojis[type] || '👍';
    },
    getReactionLabel(type) {
      const labels = {
        like: 'Like',
        love: 'Love',
        haha: 'Haha',
        wow: 'Wow',
        sad: 'Sad',
        angry: 'Angry'
      };
      return labels[type] || type;
    },
    showTooltip() {
      this.showDetail = true;
    },
    hideTooltip() {
      this.showDetail = false;
    }
  }
};
</script>

<style scoped>
.reactions-summary {
  position: relative;
  display: inline-flex;
  align-items: center;
  padding: 2px 8px;
  border-radius: 16px;
  background: #f0f2f5;
  margin: 4px 0;
}

.reactions-group {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}

.reaction-item {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 2px 6px;
  border-radius: 12px;
  background: white;
  transition: all 0.2s ease;
}

.reaction-item:hover {
  background: #e4e6eb;
  transform: scale(1.05);
}

.reaction-emoji {
  font-size: 14px;
}

.reaction-count {
  font-size: 12px;
  color: #65676b;
  font-weight: 500;
}

.reactions-tooltip {
  position: absolute;
  bottom: 100%;
  left: 0;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
  padding: 8px;
  margin-bottom: 8px;
  min-width: 150px;
  z-index: 1000;
  animation: fadeIn 0.2s ease-out;
}

.tooltip-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 4px 8px;
  font-size: 13px;
  color: #1c1e21;
}

.tooltip-item:not(:last-child) {
  border-bottom: 1px solid #f0f2f5;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }

}

@keyframes popIn {
  from {
    transform: scale(0.8);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

.reaction-item {
  animation: popIn 0.2s ease-out;
}
</style>
