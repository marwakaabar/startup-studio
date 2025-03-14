<script setup>
import axios from 'axios';
import { ref, watch } from 'vue';
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  investors: Array,
});

// Création d'une copie réactive de investors
const investorsList = ref([...props.investors]);

const emit = defineEmits(['update:investors']);

const toggleApproval = async (investorId) => {
  try {
    const response = await axios.post(`/admin/users/approve-user/${investorId}`);

    // Trouver et mettre à jour la investor dans la liste locale
    const investorIndex = investorsList.value.findIndex(s => s.id === investorId);
    if (investorIndex !== -1) {
      investorsList.value[investorIndex].approved = !investorsList.value[investorIndex].approved;

      // Émettre un événement pour signaler le changement d'état
      emit('update:investors', investorsList.value);
    }
  } catch (error) {
    console.error("Une erreur s'est produite sur le changement d'approbation.", error);
  }
};

// Surveiller les changements dans props.investors pour synchroniser investorsList
watch(() => props.investors, (newinvestors) => {
  investorsList.value = [...newinvestors];
});
</script>

<template>
  <div class="investors-container">
    <h2 class="title">Liste des investors</h2>
    <div class="investors-list">
      <div v-for="investor in investorsList" :key="investor.id" class="investor-card">
        <h3 class="investor-name">Nom : {{ investor.name }}</h3>
        <p class="investor-email">Email : {{ investor.email }}</p>
        <p class="investor-approved">
          État : <strong :class="investor.approved ? 'text-green' : 'text-red'">
            {{ investor.approved ? 'Approuvé' : 'Non approuvé' }}
          </strong>
        </p>

       <button 
  @click="toggleApproval(investor.id)" 
  :class="['approval-button', investor.approved ? 'disapprove' : 'approve']">
  {{ investor.approved ? 'Désapprouver' : 'Approuver' }}
</button>
      </div>
    </div>
  </div>
</template>
<style scoped>
.investors-container {
  padding: 20px;
  background-color: #f4f6f9;
  border-radius: 10px;
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
}

/* Mise en page en grille pour afficher 3 investors par ligne */
.investors-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); /* Min 300px, max flexible */
  gap: 20px; /* Espacement entre les cartes */
}
.title {
  text-align: center;
  font-size: 2rem;
  color: #333;
}
.investor-card {
  border: 1px solid #ddd;
  padding: 20px;
  border-radius: 10px;
  background: #fff;
  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease-in-out;
}

.investor-card:hover {
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
