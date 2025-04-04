<template>

  <Main :showNavbar="false" :showSidebar="true">
    <div>
      <p>Bienvenue, {{ props.user.name }} !</p>
    </div>
  </Main>
</template>

<script setup>
import Main from "../Layouts/Main.vue";
const props = defineProps({
  user: Object,
});
</script>
