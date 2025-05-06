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


const handleImageError = (event) => {
  const name = event.target.getAttribute('data-coach-name');
  event.target.src = `https://eu.ui-avatars.com/api/?background=D43347&color=fff&bold=true&name=${encodeURIComponent(name)}`;
};

// Ajout d'une fonction pour vérifier si l'URL est valide
const getImageUrl = (coach) => {
  return coach.image_url || `https://eu.ui-avatars.com/api/?background=D43347&color=fff&bold=true&name=${encodeURIComponent(coach.name)}`;
};
</script>

<template>
  <div class="coach-dashboard">
    <!-- Search and filter section -->
    <div class="row filters">
        <div
            class="col-lg-8 col-md-8 col-sm-8 col-12 d-lg-flex d-md-flex d-sm-flex d-block justify-content-start align-items-center">
            <div class="input-group me-2 mb-4">
                <span class="input-group-text">
                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                        <path
                            d="M17.8749 18L12.4244 12.3333M1.52344 7.61111C1.52344 8.47929 1.68792 9.33898 2.00748 10.1411C2.32705 10.9432 2.79544 11.672 3.38592 12.2859C3.9764 12.8998 4.6774 13.3867 5.4489 13.719C6.22039 14.0512 7.04728 14.2222 7.88234 14.2222C8.71741 14.2222 9.54429 14.0512 10.3158 13.719C11.0873 13.3867 11.7883 12.8998 12.3788 12.2859C12.9692 11.672 13.4376 10.9432 13.7572 10.1411C14.0768 9.33898 14.2412 8.47929 14.2412 7.61111C14.2412 6.74293 14.0768 5.88325 13.7572 5.08115C13.4376 4.27905 12.9692 3.55025 12.3788 2.93635C11.7883 2.32245 11.0873 1.83548 10.3158 1.50324C9.54429 1.171 8.71741 1 7.88234 1C7.04728 1 6.22039 1.171 5.4489 1.50324C4.6774 1.83548 3.9764 2.32245 3.38592 2.93635C2.79544 3.55025 2.32705 4.27905 2.00748 5.08115C1.68792 5.88325 1.52344 6.74293 1.52344 7.61111Z"
                            stroke="#C6C6C6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
                <input type="text" class="form-control" placeholder="Chercher">
            </div>
            <select name="" id="" class="form-select mb-4">
                <option value="" selected disabled>Catégorie</option>
            </select>
        </div>
        <div
            class="col-lg-4 col-md-4 col-sm-4 col-12 d-lg-flex d-md-flex d-sm-flex d-block justify-content-end align-items-center">
            <a :href="'#'" class="btn btn-primary me-2 mb-4">
                Ajouter une coach
            </a>
        </div>
    </div>
    
    <!-- Header row -->
    <div class="table-header">
      <div class="header-item">coach</div>
      <div class="header-item">Email</div>
      <div class="header-item">Statut</div>
      <div class="header-item">MVP</div>
      <div class="header-item">Progression Moy</div>
      <div class="header-item">Action</div>
    </div>
    
    <!-- coach rows -->
    <div class="coach-row" v-for="coach in coachsList" :key="coach.id">
      <div class="coach-profile">
        <img 
          :src="getImageUrl(coach)"
          :data-coach-name="coach.name"
          :alt="coach.name"
          class="profile-image"
          @error="handleImageError"
        />
        <span>{{ coach.name }}</span>
      </div>
      
      <div class="coach-email">{{ coach.email }}</div>
      
      <div class="coach-status">
        <span :class="status-badge">
          {{ coach.approved ? 'Approuvé' : 'Non approuvé' }}        </span>
      </div>
      
      <div class="coach-mvp">{{ coach.mvp ? 'Oui' : 'Non' }}</div>
      
      <div class="coach-progress">
        <span class="progress-text">{{ coach.progressPercentage }}%</span>
        <div class="progress-bar">
          <div class="progress-background"></div>
          <div class="progress-value" :style="{ width: coach.progressPercentage + '%' }"></div>
        </div>
      </div>
      
      <div class="coach-action">
        <button class="view-more-btn" @click="toggleApproval(coach.id)" 
        :class="['approval-button', coach.approved ? 'disapprove' : 'approve']"> {{ coach.approved ? 'Désapprouver' : 'Approuver' }}</button>
      </div>
    </div>
  </div>
  <div class="col-12 d-flex justify-content-end mt-4">
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-md">
                    <li class="page-item prev">
                        <a class="page-link" href="javascript:void(0);"><i class="tf-icon  ti ti-chevron-left"></i></a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0);">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0);">2</a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="javascript:void(0);">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0);">4</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0);">5</a>
                    </li>
                    <li class="page-item next">
                        <a class="page-link" href="javascript:void(0);"><i class="tf-icon ti ti-chevron-right"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
</template>
<style scoped>
.coach-dashboard {
  font-family: 'Poppins', sans-serif;
  background-color: #f1f9ff;
  padding: 20px;
}

.search-container {
  display: flex;
  margin-bottom: 30px;
  gap: 20px;
}

.search-box, .category-box {
  display: flex;
  align-items: center;
  background-color: white;
  border-radius: 20px;
  padding: 12px 23px;
  box-shadow: 5px 4px 15px rgba(0, 81, 131, 0.25);
  width: 260px;
  height: 60px;
}

.search-icon, .dropdown-icon {
  width: 16px;
  height: 16px;
  margin-right: 19px;
}

input {
  border: none;
  outline: none;
  font-size: 13px;
  color: rgba(0, 0, 0, 0.49);
  width: 100%;
}

.add-coach-btn {
  margin-left: auto;
}

.add-coach-btn button {
  background-color: #0093ee;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 10px 24px;
  font-weight: bold;
  font-size: 16px;
  height: 52px;
  cursor: pointer;
}

.table-header {
  display: flex;
  padding: 0 20px;
  margin-bottom: 20px;
}

.header-item {
  font-size: 20px;
  font-weight: bold;
  color: #00588f;
  flex: 1;
}

.coach-row {
  display: flex;
  align-items: center;
  background-color: white;
  border-radius: 15px;
  padding: 15px 20px;
  margin-bottom: 20px;
  box-shadow: 10px 8px 20px rgba(0, 81, 131, 0.25);
  height: 60px;
}

.coach-profile {
  display: flex;
  align-items: center;
  gap: 15px;
  flex: 1;
}

.profile-image {
  width: 59px;
  height: 59px;
  border-radius: 50%;
  object-fit: cover;
}

.coach-email, .coach-mvp {
  flex: 1;
  color: #0093ee;
}

.coach-status {
  flex: 1;
}

.status-badge {
  display: inline-block;
  padding: 10px 24px;
  border-radius: 30px;
  font-weight: bold;
  color: white;
  text-align: center;
  width: 92px;
}

.status-badge.accélérer {
  background-color: #c0c0c0;
  box-shadow: 5px 4px 10px rgba(0, 81, 131, 0.25);
}

.status-badge.incubée {
  background-color: #f69e77;
  box-shadow: 5px 4px 10px rgba(0, 81, 131, 0.25);
}

.coach-progress {
  display: flex;
  align-items: center;
  gap: 7px;
  flex: 1;
}

.progress-text {
  font-weight: bold;
  color: #0093ee;
}

.progress-bar {
  position: relative;
  width: 127px;
  height: 12px;
}

.progress-background {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: white;
  border-radius: 30px;
  box-shadow: 0px 4px 4px -1px rgba(12, 12, 13, 0.05) inset;
}

.progress-value {
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  background-color: #c7e9ff;
  border-radius: 30px;
}

.coach-action {
  flex: 1;
  text-align: center;
}

.view-more-btn {
  background-color: #0093ee;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 5px 15px;
  font-weight: bold;
  font-size: 16px;
  height: 33.6px;
  width: 104.5px;
  cursor: pointer;
}
</style>
