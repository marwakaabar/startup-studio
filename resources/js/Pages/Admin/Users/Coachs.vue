<script setup>
import axios from 'axios';
import { ref, watch } from 'vue';
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  coachs: Array,
});

// Création d'une copie réactive de coachs
const coachsList = ref([...props.coachs]);

const emit = defineEmits(['update:coachs']);

const toggleApproval = async (coachId) => {
  try {
    const response = await axios.post(`/admin/users/approve-user/${coachId}`);

    // Trouver et mettre à jour la coach dans la liste locale
    const coachIndex = coachsList.value.findIndex(s => s.id === coachId);
    if (coachIndex !== -1) {
      coachsList.value[coachIndex].approved = !coachsList.value[coachIndex].approved;

      // Émettre un événement pour signaler le changement d'état
      emit('update:coachs', coachsList.value);
    }
  } catch (error) {
    console.error("Une erreur s'est produite sur le changement d'approbation.", error);
  }
};

// Surveiller les changements dans props.coachs pour synchroniser coachsList
watch(() => props.coachs, (newcoachs) => {
  coachsList.value = [...newcoachs];
});
</script>

<template>
  <div class="coachs-container">
    <h2 class="title">Liste des coachs</h2>
    <div class="coachs-list">
      <div v-for="coach in coachsList" :key="coach.id" class="coach-card">
        <h3 class="coach-name">Nom : {{ coach.name }}</h3>
        <p class="coach-email">Email : {{ coach.email }}</p>
        <p class="coach-approved">
          État : <strong :class="coach.approved ? 'text-green' : 'text-red'">
            {{ coach.approved ? 'Approuvé' : 'Non approuvé' }}
          </strong>
        </p>

       <button 
  @click="toggleApproval(coach.id)" 
  :class="['approval-button', coach.approved ? 'disapprove' : 'approve']">
  {{ coach.approved ? 'Désapprouver' : 'Approuver' }}
</button>
      </div>
    </div>
  </div>
</template>
<style scoped>
.coachs-container {
  padding: 20px;
  background-color: #f4f6f9;
  border-radius: 10px;
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
}

/* Mise en page en grille pour afficher 3 coachs par ligne */
.coachs-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); /* Min 300px, max flexible */
  gap: 20px; /* Espacement entre les cartes */
}
.title {
  text-align: center;
  font-size: 2rem;
  color: #333;
}
.coach-card {
  border: 1px solid #ddd;
  padding: 20px;
  border-radius: 10px;
  background: #fff;
  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease-in-out;
}

.coach-card:hover {
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
