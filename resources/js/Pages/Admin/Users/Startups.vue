<script setup>
import axios from 'axios';
import { ref, watch } from 'vue';
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  startups: Array,
});

// Création d'une copie réactive de startups
const startupsList = ref([...props.startups]);

const emit = defineEmits(['update:startups']);

const toggleApproval = async (startupId) => {
  try {
    const response = await axios.post(`/admin/users/approve-user/${startupId}`);

    // Trouver et mettre à jour la startup dans la liste locale
    const startupIndex = startupsList.value.findIndex(s => s.id === startupId);
    if (startupIndex !== -1) {
      startupsList.value[startupIndex].approved = !startupsList.value[startupIndex].approved;

      // Émettre un événement pour signaler le changement d'état
      emit('update:startups', startupsList.value);
    }
  } catch (error) {
    console.error("Une erreur s'est produite sur le changement d'approbation.", error);
  }
};

// Surveiller les changements dans props.startups pour synchroniser startupsList
watch(() => props.startups, (newStartups) => {
  startupsList.value = [...newStartups];
});
</script>

<template>
  <div class="startups-container">
    <h2 class="title">Liste des Startups</h2>
    <div class="startups-list">
      <div v-for="startup in startupsList" :key="startup.id" class="startup-card">
        <h3 class="startup-name">Nom : {{ startup.name }}</h3>
        <p class="startup-email">Email : {{ startup.email }}</p>
        <p class="startup-approved">
          État : <strong :class="startup.approved ? 'text-green' : 'text-red'">
            {{ startup.approved ? 'Approuvé' : 'Non approuvé' }}
          </strong>
        </p>

       <button 
  @click="toggleApproval(startup.id)" 
  :class="['approval-button', startup.approved ? 'disapprove' : 'approve']">
  {{ startup.approved ? 'Désapprouver' : 'Approuver' }}
</button>
      </div>
    </div>
  </div>
</template>
<style scoped>
.startups-container {
  padding: 20px;
  background-color: #f4f6f9;
  border-radius: 10px;
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
}

/* Mise en page en grille pour afficher 3 startups par ligne */
.startups-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); /* Min 300px, max flexible */
  gap: 20px; /* Espacement entre les cartes */
}
.title {
  text-align: center;
  font-size: 2rem;
  color: #333;
}
.startup-card {
  border: 1px solid #ddd;
  padding: 20px;
  border-radius: 10px;
  background: #fff;
  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease-in-out;
}

.startup-card:hover {
  transform: translateY(-5px);
}

.text-green {
  color: #28a745;
  font-weight: bold;
}

.text-red {
  color: #dc3545;
  font-weight: bold;
}

.approval-button {
  margin-top: 10px;
  padding: 10px 20px;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: all 0.3s ease-in-out;
  font-weight: bold;
  width: 100%;
  text-align: center;
}

.approval-button.approve {
  background-color: #28a745;
}

.approval-button.approve:hover {
  background-color: #218838;
}

.approval-button.disapprove {
  background-color: #dc3545;
}

.approval-button.disapprove:hover {
  background-color: #c82333;
}
</style>
