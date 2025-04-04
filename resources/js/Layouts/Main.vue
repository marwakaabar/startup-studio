<template>
  <div class="min-h-screen flex flex-col">
    <Navbar v-if="showNavbar" :user="user" />
    <div class="flex flex-1">
      <Sidebar v-if="props.showSidebar" />
      <main 
        class="flex-1 p-6"
        :class="{
          'ml-[280px]': props.showSidebar,
          'ml-0': !props.showSidebar
        }"
      >
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import Navbar from "../Components/Navbar.vue";
import Sidebar from "../Components/SideBarUser.vue";
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

// Définir les propriétés pour contrôler l'affichage
const props = defineProps({
  showSidebar: {
    type: Boolean,
    default: false,
  },
  showNavbar: { 
    type: Boolean, 
    default: true 
  }
});

// Récupérer l'utilisateur connecté depuis Inertia
const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);
</script>
