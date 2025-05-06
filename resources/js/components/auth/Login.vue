<template>
  <div class="container-xxl flex-grow-1 container-p-y auth-container" style="height:100vh;">
    <img src="/assets/img/dash/circletop.png" alt="" class="circle circletop">
    <img src="/assets/img/dash/circleright.png" alt="" class="circle circleright">
    <img src="/assets/img/dash/circletopright.png" alt="" class="circle circletopright">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-lg-5 col-md-6 col-12">
        <div class="card cardLogin">
          <div class="card-body">
            <div v-if="errors.length > 0" class="alert alert-danger alert-dismissible" role="alert">
              <ul>
                <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
              </ul>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <div v-if="message" class="form-group">
              <div class="alert alert-success dark alert-dismissible fade show" role="alert">
                <span>{{ message }}</span>
                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            </div>
            <div v-if="tokenExpire" class="form-group">
              <div class="alert alert-danger dark alert-dismissible fade show" role="alert">
                <span>{{ tokenExpire }}</span>
                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            </div>
            <div v-if="errorMessage" class="alert alert-danger">
              {{ errorMessage }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <form @submit.prevent="login">
              <h5 class="mb-5 title">Accéder à mon compte</h5>
              <div class="input-group mb-4">
                <span class="input-group-text">
                  <img src="/assets/img/icons/Mail-2.png" alt="">
                </span>
                <input type="text" class="form-control" placeholder="Exemple@gmail.com" v-model="form.email">
              </div>
              <div class="input-group mb-2">
                <span class="input-group-text">
                  <img src="/assets/img/icons/lock.png" alt="">
                </span>
                <input type="password" class="form-control" placeholder="Mot de passe" v-model="form.password">
              </div>
              <div class="mb-4 d-flex justify-content-end">
                <a href="/forgot-password">
                  <small>Mot de passe oublié?</small>
                </a>
              </div>
              <button class="btn btn-primary w-100 mb-4" type="submit">Se connecter</button>
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
  name: 'LoginComponent',
  data() {
    return {
      form: {
        email: '',
        password: ''
      },
      errors: [],
      message: '',
      tokenExpire: '',
      errorMessage: ''
    }
  },
  mounted() {
    // Récupérer les messages de la session si nécessaire
    if (window.sessionStorage.getItem('message')) {
      this.message = window.sessionStorage.getItem('message');
      window.sessionStorage.removeItem('message');
    }
    if (window.sessionStorage.getItem('token_expire')) {
      this.tokenExpire = window.sessionStorage.getItem('token_expire');
      window.sessionStorage.removeItem('token_expire');
    }
    if (window.sessionStorage.getItem('error')) {
      this.errorMessage = window.sessionStorage.getItem('error');
      window.sessionStorage.removeItem('error');
    }
  },
  methods: {
    login() {
      this.errors = [];
      
      // Validation basique
      if (!this.form.email) {
        this.errors.push('L\'adresse email est requise');
      }
      if (!this.form.password) {
        this.errors.push('Le mot de passe est requis');
      }
      
      if (this.errors.length === 0) {
        // Soumettre le formulaire via axios
        axios.post('/login', this.form)
          .then(response => {
            // S'assurer que response.data existe et est un objet valide
            if (response.data && typeof response.data === 'object') {
              if (response.data.success) {
                window.location.href = response.data.redirect || '/dashboard';
              } else {
                this.errors.push(response.data.message || 'Une erreur est survenue');
              }
            } else {
              // Si la réponse n'est pas un objet JSON valide
              console.error('Réponse invalide reçue du serveur:', response);
              this.errors.push('Format de réponse invalide reçu du serveur');
            }
          })
          .catch(error => {
            console.error('Erreur de connexion:', error);
            
            if (error.response) {
              // Si le serveur répond avec un code de redirection (302)
              if (error.response.status === 302 || error.response.status === 301) {
                // Rediriger vers la page indiquée ou le tableau de bord par défaut
                window.location.href = error.response.headers.location || '/dashboard';
                return;
              }
              
              if (error.response.data) {
                if (error.response.data.errors) {
                  for (const key in error.response.data.errors) {
                    this.errors.push(error.response.data.errors[key][0]);
                  }
                } else if (error.response.data.message) {
                  this.errors.push(error.response.data.message);
                }
              }
            } else if (error.request) {
              // La requête a été faite mais pas de réponse reçue
              this.errors.push('Erreur de connexion au serveur. Veuillez réessayer plus tard.');
            } else {
              // Erreur lors de la configuration de la requête
              this.errors.push('Une erreur est survenue lors de la connexion. Veuillez réessayer.');
            }
          });
      }
    }
  }
}
</script>

<style scoped>
/* Vous pouvez conserver les styles existants ou les adapter selon vos besoins */
</style>
