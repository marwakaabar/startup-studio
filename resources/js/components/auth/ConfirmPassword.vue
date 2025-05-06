<template>
  <div class="container-xxl flex-grow-1 container-p-y auth-container" style="height:100vh;">
    <img src="/assets/img/dash/circletop.png" alt="" class="circle circletop">
    <img src="/assets/img/dash/circleright.png" alt="" class="circle circleright">
    <img src="/assets/img/dash/circletopright.png" alt="" class="circle circletopright">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-lg-5 col-md-6 col-12">
        <div class="card cardLogin">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-center mb-4">
              <img src="/assets/img/dash/logo.png" alt="Logo" class="me-2" style="width: 40px;">
              <h4 class="mb-0">Confirmation de sécurité</h4>
            </div>

            <div v-if="errors.length > 0" class="alert alert-danger alert-dismissible" role="alert">
              <ul class="mb-0">
                <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
              </ul>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            <p class="mb-4">Cette action nécessite une confirmation de votre mot de passe pour des raisons de sécurité.</p>

            <form @submit.prevent="confirmPassword">
              <div class="input-group mb-4">
                <span class="input-group-text">
                  <img src="/assets/img/icons/lock.png" alt="">
                </span>
                <input type="password" class="form-control" placeholder="Mot de passe" v-model="form.password" required>
              </div>

              <button class="btn btn-primary w-100 mb-4" type="submit" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Confirmer
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ConfirmPasswordComponent',
  data() {
    return {
      form: {
        password: ''
      },
      errors: [],
      loading: false
    }
  },
  methods: {
    confirmPassword() {
      this.loading = true;
      this.errors = [];
      
      // Validation basique
      if (!this.form.password) {
        this.errors.push('Le mot de passe est requis');
        this.loading = false;
        return;
      }
      
      // Soumettre le formulaire via axios
      axios.post('/confirm-password', this.form)
        .then(response => {
          this.loading = false;
          if (response.data.status === 'success') {
            // Rediriger vers la page demandée ou le tableau de bord
            window.location.href = response.data.redirect || '/dashboard';
          } else {
            this.errors.push(response.data.message || 'Une erreur est survenue lors de la confirmation du mot de passe.');
          }
        })
        .catch(error => {
          this.loading = false;
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
            this.errors.push('Une erreur est survenue lors de la confirmation du mot de passe. Veuillez réessayer.');
          }
        });
    }
  }
}
</script>

<style scoped>
/* Vous pouvez conserver les styles existants ou les adapter selon vos besoins */
</style>
