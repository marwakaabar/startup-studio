<template>
  <div class="container-xxl flex-grow-1 container-p-y auth-container d-flex justify-content-center align-items-center" style="height:100vh;">
    <img src="/assets/img/dash/circletop.png" alt="" class="circle circletop">
    <img src="/assets/img/dash/circleright.png" alt="" class="circle circleright">
    <img src="/assets/img/dash/circletopright.png" alt="" class="circle circletopright">
    <div class="row d-flex justify-content-center align-items-center w-100">
      <div class="col-lg-5 col-md-6 col-12">
        <div class="card cardLogin">
          <div class="card-body">
            <form @submit.prevent="sendResetLink">
              <div v-if="message" class="alert alert-success alert-dismissible" role="alert">
                {{ message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <div v-if="error" class="alert alert-danger alert-dismissible" role="alert">
                {{ error }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <div v-if="errors.email" class="alert alert-danger alert-dismissible" role="alert">
                {{ errors.email }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <h5 class="mb-5 title">Mot de passe oublié</h5>
              <div class="input-group mb-4">
                <span class="input-group-text">
                  <img src="/assets/img/icons/Mail-2.png" alt="">
                </span>
                <input type="text" class="form-control" placeholder="Exemple@gmail.com" v-model="form.email">
              </div>

              <button class="btn btn-primary w-100 mb-4" type="submit" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Envoyer le lien de réinitialisation
              </button>
            </form>
            <div class="text-center mb-4">
              <p class="text">Vous n'êtes pas encore client ? <a href="/register">Créer un compte</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ForgotPasswordComponent',
  data() {
    return {
      form: {
        email: ''
      },
      message: '',
      error: '',
      errors: {
        email: ''
      },
      loading: false
    }
  },
  methods: {
    sendResetLink() {
      this.loading = true;
      this.message = '';
      this.error = '';
      this.errors.email = '';
      
      // Validation basique
      if (!this.form.email) {
        this.errors.email = 'L\'adresse email est requise';
        this.loading = false;
        return;
      }
      
      // Soumettre le formulaire via axios
      axios.post('/forgot-password', this.form)
        .then(response => {
          this.loading = false;
          if (response.data.status === 'success') {
            this.message = response.data.message || 'Un lien de réinitialisation a été envoyé à votre adresse email.';
            this.form.email = '';
          } else {
            this.error = response.data.message || 'Une erreur est survenue lors de l\'envoi du lien de réinitialisation.';
          }
        })
        .catch(error => {
          this.loading = false;
          // Si le serveur répond avec un code de redirection (302)
          if (error.response && (error.response.status === 302 || error.response.status === 301)) {
            // Rediriger vers la page indiquée ou le tableau de bord par défaut
            window.location.href = error.response.headers.location || '/dashboard';
            return;
          }
          
          if (error.response && error.response.data) {
            if (error.response.data.errors && error.response.data.errors.email) {
              this.errors.email = error.response.data.errors.email[0];
            } else if (error.response.data.message) {
              this.error = error.response.data.message;
            } else if (error.response.data.status === 'error') {
              this.error = error.response.data.message || 'Une erreur est survenue lors de l\'envoi du lien de réinitialisation.';
            }
          } else if (error.request) {
            // La requête a été faite mais pas de réponse reçue
            this.error = 'Erreur de connexion au serveur. Veuillez réessayer plus tard.';
          } else {
            this.error = 'Une erreur est survenue lors de l\'envoi du lien de réinitialisation. Veuillez réessayer.';
          }
        });
    }
  }
}
</script>

<style scoped>
/* Vous pouvez conserver les styles existants ou les adapter selon vos besoins */
</style>
