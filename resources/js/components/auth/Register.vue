<template>
  <div class="container-xxl flex-grow-1 container-p-y auth-container" style="height:100vh;">
    <img src="/assets/img/dash/circletop.png" alt="" class="circle circletop">
    <img src="/assets/img/dash/circleright.png" alt="" class="circle circleright">
    <img src="/assets/img/dash/circletopright.png" alt="" class="circle circletopright">
    <div class="row d-flex justify-content-center w-100">
      <div class="col-lg-5 col-md-6 col-12">
        <div class="card cardLogin mb-5">
          <div class="card-body">
            <div v-if="errors.length > 0" class="alert alert-danger alert-dismissible" role="alert">
              <ul>
                <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
              </ul>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            
            <h5 class="mb-4 title text-center">Créer un compte</h5>
            
            <!-- Step indicator -->
            <div class="step-indicator">
              <div class="step" :class="{ active: currentStep === 1 }" data-step="1">
                <div class="step-circle">1</div>
                <div class="step-title">Choisir rôle</div>
              </div>
              <div class="step" :class="{ active: currentStep === 2 }" data-step="2">
                <div class="step-circle">2</div>
                <div class="step-title">Vos informations</div>
              </div>
            </div>
            
            <form @submit.prevent="register">
              <input type="hidden" v-model="form.role">
              
              <!-- Step 1: Role selection -->
              <div class="form-step" :class="{ active: currentStep === 1 }" id="step-1">
                <div class="text-center mb-4">
                  <h6>Sélectionnez votre rôle</h6>
                </div>
                
                <div class="role-cards">
                  <div class="role-card" :class="{ selected: form.role === 'startup' }" @click="selectRole('startup')">
                    <div class="role-check"></div>
                    <div class="role-icon">
                      <span class="iconify" data-icon="mdi:rocket-launch"></span>
                    </div>
                    <div class="role-title">Startup</div>
                    <div class="role-description">
                      Pour les entrepreneurs innovants cherchant à développer leur projet
                    </div>
                  </div>
                  
                  <div class="role-card" :class="{ selected: form.role === 'coach' }" @click="selectRole('coach')">
                    <div class="role-check"></div>
                    <div class="role-icon">
                      <span class="iconify" data-icon="mdi:account-tie"></span>
                    </div>
                    <div class="role-title">Coach</div>
                    <div class="role-description">
                      Pour les mentors et experts qui guident les entrepreneurs
                    </div>
                  </div>
                  
                  <div class="role-card" :class="{ selected: form.role === 'investisseur' }" @click="selectRole('investisseur')">
                    <div class="role-check"></div>
                    <div class="role-icon">
                      <span class="iconify" data-icon="mdi:chart-line"></span>
                    </div>
                    <div class="role-title">Investisseur</div>
                    <div class="role-description">
                      Pour ceux qui souhaitent investir dans des projets prometteurs
                    </div>
                  </div>
                </div>
                
                <div class="form-nav-buttons">
                  <div></div>
                  <button type="button" class="btn btn-primary" @click="nextStep">Suivant</button>
                </div>
              </div>
              
              <!-- Step 2: Complete information -->
              <div class="form-step" :class="{ active: currentStep === 2 }" id="step-2">
                <div class="text-center mb-4">
                  <h6>Complétez vos informations</h6>
                </div>
                
                <div class="input-group mb-4">
                  <span class="input-group-text">
                    <img src="/assets/img/icons/person-gray.png" alt="">
                  </span>
                  <input type="text" class="form-control" placeholder="Nom et prénom" v-model="form.name" required>
                </div>
                <div class="input-group mb-4">
                  <span class="input-group-text">
                    <img src="/assets/img/icons/Mail-2.png" alt="">
                  </span>
                  <input type="email" class="form-control" placeholder="Adresse email" v-model="form.email" required>
                </div>
                
                <!-- Champs conditionnels basés sur le rôle -->
                <div v-if="form.role === 'startup'" class="role-fields mb-4">
                  <div class="input-group">
                    <span class="input-group-text">
                      <span class="iconify" data-icon="mdi:domain" style="font-size: 20px;"></span>
                    </span>
                    <input type="text" class="form-control" placeholder="Nom de domaine" v-model="form.domain_name">
                  </div>
                </div>
                
                <div v-if="form.role === 'coach'" class="role-fields mb-4">
                  <div class="input-group">
                    <span class="input-group-text">
                      <span class="iconify" data-icon="mdi:certificate" style="font-size: 20px;"></span>
                    </span>
                    <input type="text" class="form-control" placeholder="Spécialité" v-model="form.specialty">
                  </div>
                </div>
                
                <div v-if="form.role === 'investisseur'" class="role-fields mb-4">
                  <div class="input-group">
                    <span class="input-group-text">
                      <span class="iconify" data-icon="mdi:eye" style="font-size: 20px;"></span>
                    </span>
                    <select class="form-select" v-model="form.visibility">
                      <option value="public">Public</option>
                      <option value="private">Privé</option>
                    </select>
                  </div>
                </div>

                <div class="input-group mb-4">
                  <span class="input-group-text">
                    <img src="/assets/img/icons/lock.png" alt="">
                  </span>
                  <input type="password" class="form-control" placeholder="Mot de passe" v-model="form.password" required>
                </div>
                <div class="input-group mb-4">
                  <span class="input-group-text">
                    <img src="/assets/img/icons/lock.png" alt="">
                  </span>
                  <input type="password" class="form-control" placeholder="Confirmer mot de passe"
                    v-model="form.password_confirmation" required>
                </div>
                
                <div class="form-nav-buttons">
                  <button type="button" class="btn btn-outline-secondary" @click="prevStep">Revenir</button>
                  <button type="submit" class="btn btn-primary fw-bold">S'inscrire</button>
                </div>
              </div>
            </form>
            
            <div class="text-center mt-4">
              <p class="text">Vous avez un compte ? <a href="/login">Se connecter</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RegisterComponent',
  data() {
    return {
      currentStep: 1,
      form: {
        role: '',
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        domain_name: '',
        specialty: '',
        visibility: 'public'
      },
      errors: []
    }
  },
  methods: {
    selectRole(role) {
      this.form.role = role;
    },
    validateRoleSelection() {
      if (!this.form.role) {
        this.errors = ['Veuillez sélectionner un rôle pour continuer.'];
        return false;
      }
      return true;
    },
    nextStep() {
      if (this.validateRoleSelection()) {
        this.currentStep = 2;
      }
    },
    prevStep() {
      this.currentStep = 1;
    },
    register() {
      this.errors = [];
      
      // Validation basique
      if (!this.form.name) {
        this.errors.push('Le nom est requis');
      }
      if (!this.form.email) {
        this.errors.push('L\'adresse email est requise');
      }
      if (!this.form.password) {
        this.errors.push('Le mot de passe est requis');
      }
      if (this.form.password !== this.form.password_confirmation) {
        this.errors.push('Les mots de passe ne correspondent pas');
      }
      
      if (this.errors.length === 0) {
        // Soumettre le formulaire via axios
        axios.post('/register', this.form)
          .then(response => {
            if (response.data.success) {
              window.location.href = response.data.redirect || '/dashboard';
            } else {
              this.errors.push(response.data.message || 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.');
            }
          })
          .catch(error => {
            // Si le serveur répond avec un code de redirection (302)
            if (error.response && (error.response.status === 302 || error.response.status === 301)) {
              // Rediriger vers la page indiquée ou le tableau de bord par défaut
              window.location.href = error.response.headers.location || '/dashboard';
              return;
            }
            
            if (error.response && error.response.data) {
              if (error.response.data.errors) {
                for (const key in error.response.data.errors) {
                  this.errors.push(error.response.data.errors[key][0]);
                }
              } else if (error.response.data.message) {
                this.errors.push(error.response.data.message);
              }
            } else if (error.request) {
              // La requête a été faite mais pas de réponse reçue
              this.errors.push('Erreur de connexion au serveur. Veuillez réessayer plus tard.');
            } else {
              this.errors.push('Une erreur est survenue lors de l\'inscription. Veuillez réessayer.');
            }
          });
      }
    }
  }
}
</script>

<style scoped>
.form-step {
  display: none;
}

.form-step.active {
  display: block;
}

.step-indicator {
  display: flex;
  justify-content: center;
  margin-bottom: 1.5rem;
}

.step {
  width: 100px;
  text-align: center;
  position: relative;
}

.step:not(:last-child):after {
  content: '';
  position: absolute;
  top: 10px;
  right: -40%;
  width: 80%;
  height: 2px;
  background-color: #e0e0e0;
}

.step.active:not(:last-child):after {
  background-color: #696cff;
}

.step-circle {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background-color: #e0e0e0;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 5px;
  color: #fff;
  font-size: 12px;
  font-weight: bold;
}

.step.active .step-circle, .step.completed .step-circle {
  background-color: #696cff;
}

.step-title {
  font-size: 12px;
  color: #6c757d;
}

.step.active .step-title {
  color: #696cff;
  font-weight: 600;
}

.form-nav-buttons {
  display: flex;
  justify-content: space-between;
  margin-top: 1.5rem;
}

/* Styles pour les cartes de rôle */
.role-cards {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  margin: 1.5rem 0;
  gap: 10px;
}

.role-card {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 1rem 0.5rem;
  flex: 1;
  min-width: 0;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  background-color: #fff;
}

.role-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  border-color: #ccc;
}

.role-card.selected {
  border: 2px solid #696cff;
  background-color: rgba(105, 108, 255, 0.05);
}

.role-card input[type="radio"] {
  position: absolute;
  opacity: 0;
}

.role-icon {
  font-size: 2rem;
  margin-bottom: 0.75rem;
  color: #696cff;
  position: relative;
  z-index: 1;
}

.role-title {
  font-weight: 600;
  font-size: 0.9rem;
  margin-bottom: 0.25rem;
  color: #3E3F42;
}

.role-description {
  font-size: 0.75rem;
  color: #6c757d;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.role-card::before {
  content: '';
  position: absolute;
  top: -20px;
  right: -20px;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background-color: rgba(105, 108, 255, 0.1);
  z-index: 0;
  opacity: 0;
  transition: all 0.3s ease;
}

.role-card:hover::before,
.role-card.selected::before {
  transform: scale(8);
  opacity: 1;
}

.role-check {
  position: absolute;
  top: 10px;
  right: 10px;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 2px solid #e0e0e0;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.role-card.selected .role-check {
  border-color: #696cff;
  background-color: #696cff;
}

.role-check::after {
  content: '';
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: white;
  opacity: 0;
  transition: all 0.3s ease;
}

.role-card.selected .role-check::after {
  opacity: 1;
}

/* Media query pour les petits écrans */
@media (max-width: 576px) {
  .role-cards {
    flex-direction: column;
  }
  
  .role-card {
    margin-bottom: 10px;
  }
}
</style>
