<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Sidebar from '../../Components/Sidebar.vue';
import Coachs from './Users/Coachs.vue';
import Investors from './Users/Investors.vue';
import Startups from './Users/Startups.vue';

const user = ref({});
const coachs = ref([]);
const investors = ref([]);
const startups = ref([]);
const selectedPage = ref('dashboard');

const fetchUsers = async () => {
  try {
    const coachsRes = await axios.get('/admin/users/coachs');
    const investorsRes = await axios.get('/admin/users/investors');
    const startupsRes = await axios.get('/admin/users/startups');

    coachs.value = coachsRes.data;
    investors.value = investorsRes.data;
    startups.value = startupsRes.data;
  } catch (error) {
    console.error("Erreur lors de la récupération des données :", error);
  }
};

onMounted(() => {
  fetchUsers();
});

const updateContent = (page) => {
  selectedPage.value = page;
};

const props = defineProps({
  user: Object,  
});

</script>

<template>
  <div class="admin-dashboard">
    <Sidebar @change-page="updateContent" />
    <div class="content">
      <div v-if="selectedPage === 'dashboard'">
        <p>Bienvenue, {{ props.user.name }} !</p>
      </div>
      <div v-else-if="selectedPage === 'coachs'">
        <Coachs :coachs="coachs" />
      </div>
      <div v-else-if="selectedPage === 'investors'">
        <Investors :investors="investors" />
      </div>
      <div v-else-if="selectedPage === 'startups'">
        <Startups :startups="startups" />
      </div>
    </div>
  </div>
</template>

<style scoped>
.admin-dashboard {
  display: flex;
}

.content {
  margin-left: 250px;
  padding: 20px;
  width: 100%;
}
</style>
