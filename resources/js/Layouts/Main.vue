<template>
  <Navbar v-if="showNavbar"  />
  <Sidebar v-if="props.showSidebar" />
  <main class="p-6 mx-auto max-w-screen-lg">
    <slot />
  </main>
</template>

<script setup>
import Navbar from "../Components/Navbar.vue";
import Sidebar from "../Components/SideBarUser.vue";

// Définir les propriétés pour contrôler l'affichage
const props = defineProps({
  showSidebar: {
    type: Boolean,
    default: false,
  },

    showNavbar:  { type: Boolean, default: true },

});

</script>

<style scoped>
/* Ajoutez des styles si nécessaire */
</style>
