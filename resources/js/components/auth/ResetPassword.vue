<template>
  <div class="container-xxl flex-grow-1 container-p-y auth-container" style="height:100vh;">
    <img src="/assets/img/dash/circletop.png" alt="" class="circle circletop">
    <img src="/assets/img/dash/circleright.png" alt="" class="circle circleright">
    <img src="/assets/img/dash/circletopright.png" alt="" class="circle circletopright">
    <div class="row d-flex justify-content-center w-100">
      <div class="col-lg-5 col-md-6 col-12">
        <div class="card cardLogin">
          <div class="card-body">
            <span class="salut mb-1">Réinitialiser le mot de passe 🔒</span>
            <p class="mb-4">Pour <span class="fw-medium">{{ email }}</span></p>
            <form @submit.prevent="resetPassword">
              <div v-if="errors.length > 0" class="alert alert-danger alert-dismissible" role="alert">
                <ul class="mb-0">
                  <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <input type="hidden" v-model="form.token">
              <input type="hidden" v-model="form.email">
              <div class="input-group mb-4">
                <span class="input-group-text">
                  <img src="/assets/img/icons/lock.png" alt="">
                </span>
                <input type="password" class="form-control" placeholder="Nouveau mot de passe"
                  v-model="form.password" required>
              </div>
              <div class="input-group mb-4">
                <span class="input-group-text">
                  <img src="/assets/img/icons/lock.png" alt="">
                </span>
                <input type="password" class="form-control" placeholder="Confirmer le mot de passe"
                  v-model="form.password_confirmation" required>
              </div>
              <button class="btn btn-primary w-100 mb-4" type="submit" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Définir un nouveau mot de passe
              </button>
            </form>
            <div class="text-center mb-4">
              <p class="text">Retour à la <a href="/login">page de connexion</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ResetPasswordComponent',
  props: {
    token: {
      type: String,
      required: true
    },
    email: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      form: {
        token: this.token,
        email: this.email,
        password: '',
        password_confirmation: ''
      },
      errors: [],
      loading: false
    }
  },
  methods: {
    resetPassword() {
      this.loading = true;
      this.errors = [];
      
      // Validation basique
      if (!this.form.password) {
        this.errors.push('Le mot de passe est requis');
        this.loading = false;
        return;
      }
      
      if (this.form.password !== this.form.password_confirmation) {
        this.errors.push('Les mots de passe ne correspondent pas');
        this.loading = false;
        return;
      }
      
      // Soumettre le formulaire via axios
      axios.post('/reset-password', this.form)
        .then(response => {
          this.loading = false;
          if (response.data.status === 'success') {
            // Afficher un message de succès et rediriger vers la page de connexion
            alert(response.data.message || 'Votre mot de passe a été réinitialisé avec succès');
            window.location.href = '/login';
          } else {
            this.errors.push(response.data.message || 'Une erreur est survenue lors de la réinitialisation du mot de passe.');
          }
        })
        .catch(error => {
          this.loading = false;
          // Si le serveur répond avec un code de redirection (302)
          if (error.response && (error.response.status === 302 || error.response.status === 301)) {
            // Rediriger vers la page indiquée ou la page de connexion par défaut
            window.location.href = error.response.headers.location || '/login';
            return;
          }
          
          if (error.response && error.response.data) {
            if (error.response.data.errors) {
              for (const key in error.response.data.errors) {
                this.errors.push(error.response.data.errors[key][0]);
              }
            } else if (error.response.data.message) {
              this.errors.push(error.response.data.message);
            } else if (error.response.data.status === 'error') {
              this.errors.push(error.response.data.message || 'Une erreur est survenue lors de la réinitialisation du mot de passe.');
            }
          } else if (error.request) {
            // La requête a été faite mais pas de réponse reçue
            this.errors.push('Erreur de connexion au serveur. Veuillez réessayer plus tard.');
          } else {
            this.errors.push('Une erreur est survenue lors de la réinitialisation du mot de passe. Veuillez réessayer.');
          }
        });
    }
  }
}
</script>

<style scoped>
/* Vous pouvez conserver les styles existants ou les adapter selon vos besoins */
</style>
