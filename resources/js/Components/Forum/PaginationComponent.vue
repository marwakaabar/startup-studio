<template>
  <div class="pagination">
    <!-- Bouton Précédent -->
    <button @click="previousPage" :disabled="currentPage === 1" class="pagination-item">
      <i class="fas fa-chevron-left"></i>
    </button>

    <!-- Numéros de page -->
    <button v-for="page in pages" :key="page" 
            @click="goToPage(page)" 
            :class="{ active: page === currentPage }"
            class="pagination-item">
      {{ page }}
    </button>

    <!-- Bouton Suivant -->
    <button @click="nextPage" :disabled="currentPage === totalPages" class="pagination-item">
      <i class="fas fa-chevron-right"></i>
    </button>
  </div>
</template>

<script>
import { computed } from 'vue';

export default {
  name: 'PaginationComponent',
  props: {
    currentPage: {
      type: Number,
      required: true,
    },
    totalPages: {
      type: Number,
      required: true,
    },
    maxVisiblePages: {
      type: Number,
      default: 5,
    }
  },
  emits: ['update:currentPage'],
  setup(props, { emit }) {
    const pages = computed(() => {
      const { currentPage, totalPages, maxVisiblePages } = props;
      let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
      let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

      if (endPage - startPage < maxVisiblePages - 1) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
      }

      return Array.from({ length: endPage - startPage + 1 }, (_, i) => startPage + i);
    });

    const goToPage = (page) => {
      if (page >= 1 && page <= props.totalPages && page !== props.currentPage) {
        emit('update:currentPage', page);
      }
    };

    const previousPage = () => {
      if (props.currentPage > 1) goToPage(props.currentPage - 1);
    };

    const nextPage = () => {
      if (props.currentPage < props.totalPages) goToPage(props.currentPage + 1);
    };

    return {
      pages,
      goToPage,
      previousPage,
      nextPage
    };
  }
};
</script>

<style scoped>

</style>
