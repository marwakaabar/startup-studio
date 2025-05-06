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

const handleImageError = (event) => {
  const name = event.target.getAttribute('data-investor-name');
  event.target.src = `https://eu.ui-avatars.com/api/?background=D43347&color=fff&bold=true&name=${encodeURIComponent(name)}`;
};

// Ajout d'une fonction pour vérifier si l'URL est valide
const getImageUrl = (investor) => {
  return investor.image_url || `https://eu.ui-avatars.com/api/?background=D43347&color=fff&bold=true&name=${encodeURIComponent(investor.name)}`;
};
</script>

<template>
  <div class="investor-dashboard">
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
                Ajouter un Investisseur
            </a>
        </div>
    </div>
    
    <!-- Header row -->
    <div class="table-header">
      <div class="header-item">Investisseur</div>
      <div class="header-item">Email</div>
      <div class="header-item">Statut</div>
      <div class="header-item">MVP</div>
      <div class="header-item">Progression Moy</div>
      <div class="header-item">Action</div>
    </div>
    
    <!-- investor rows -->
    <div class="investor-row" v-for="investor in investorsList" :key="investor.id">
      <div class="investor-profile">
        <img 
          :src="getImageUrl(investor)"
          :data-investor-name="investor.name"
          :alt="investor.name"
          class="profile-image"
          @error="handleImageError"
        />
        <span>{{ investor.name }}</span>
      </div>
      
      <div class="investor-email">{{ investor.email }}</div>
      
      <div class="investor-status">
        <span :class="status-badge">
          {{ investor.approved ? 'Approuvé' : 'Non approuvé' }}        </span>
      </div>
      
      <div class="investor-mvp">{{ investor.mvp ? 'Oui' : 'Non' }}</div>
      
      <div class="investor-progress">
        <span class="progress-text">{{ investor.progressPercentage }}%</span>
        <div class="progress-bar">
          <div class="progress-background"></div>
          <div class="progress-value" :style="{ width: investor.progressPercentage + '%' }"></div>
        </div>
      </div>
      
      <div class="investor-action">
        <button class="view-more-btn" @click="toggleApproval(investor.id)" 
        :class="['approval-button', investor.approved ? 'disapprove' : 'approve']"> {{ investor.approved ? 'Désapprouver' : 'Approuver' }}</button>
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
.investor-dashboard {
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

.add-investor-btn {
  margin-left: auto;
}

.add-investor-btn button {
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

.investor-row {
  display: flex;
  align-items: center;
  background-color: white;
  border-radius: 15px;
  padding: 15px 20px;
  margin-bottom: 20px;
  box-shadow: 10px 8px 20px rgba(0, 81, 131, 0.25);
  height: 60px;
}

.investor-profile {
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

.investor-email, .investor-mvp {
  flex: 1;
  color: #0093ee;
}

.investor-status {
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

.investor-progress {
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

.investor-action {
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
