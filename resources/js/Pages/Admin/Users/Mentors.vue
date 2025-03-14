<script setup>
import axios from 'axios';
import { ref, watch } from 'vue';
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  mentors: Array,
});

// Création d'une copie réactive de mentors
const mentorsList = ref([...props.mentors]);

const emit = defineEmits(['update:mentors']);

const toggleApproval = async (mentorId) => {
  try {
    const response = await axios.post(`/admin/users/approve-user/${mentorId}`);

    // Trouver et mettre à jour la mentor dans la liste locale
    const mentorIndex = mentorsList.value.findIndex(s => s.id === mentorId);
    if (mentorIndex !== -1) {
      mentorsList.value[mentorIndex].approved = !mentorsList.value[mentorIndex].approved;

      // Émettre un événement pour signaler le changement d'état
      emit('update:mentors', mentorsList.value);
    }
  } catch (error) {
    console.error("Une erreur s'est produite sur le changement d'approbation.", error);
  }
};

// Surveiller les changements dans props.mentors pour synchroniser mentorsList
watch(() => props.mentors, (newmentors) => {
  mentorsList.value = [...newmentors];
});
</script>

<template>
  <div class="mentors-container">
    <h2 class="title">Liste des mentors</h2>
    <div class="mentors-list">
      <div v-for="mentor in mentorsList" :key="mentor.id" class="mentor-card">
        <h3 class="mentor-name">Nom : {{ mentor.name }}</h3>
        <p class="mentor-email">Email : {{ mentor.email }}</p>
        <p class="mentor-approved">
          État : <strong :class="mentor.approved ? 'text-green' : 'text-red'">
            {{ mentor.approved ? 'Approuvé' : 'Non approuvé' }}
          </strong>
        </p>

       <button 
  @click="toggleApproval(mentor.id)" 
  :class="['approval-button', mentor.approved ? 'disapprove' : 'approve']">
  {{ mentor.approved ? 'Désapprouver' : 'Approuver' }}
</button>
      </div>
    </div>
  </div>
</template>
<style scoped>
.mentors-container {
  padding: 20px;
  background-color: #f4f6f9;
  border-radius: 10px;
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
}

/* Mise en page en grille pour afficher 3 mentors par ligne */
.mentors-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); /* Min 300px, max flexible */
  gap: 20px; /* Espacement entre les cartes */
}
.title {
  text-align: center;
  font-size: 2rem;
  color: #333;
}
.mentor-card {
  border: 1px solid #ddd;
  padding: 20px;
  border-radius: 10px;
  background: #fff;
  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease-in-out;
}

.mentor-card:hover {
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
