<template>
  <div class="container-xxl flex-grow-1 container-p-y auth-container" style="height:100vh;">
    <img src="/assets/img/dash/circletop.png" alt="" class="circle circletop">
    <img src="/assets/img/dash/circleright.png" alt="" class="circle circleright">
    <img src="/assets/img/dash/circletopright.png" alt="" class="circle circletopright">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-lg-5 col-md-6 col-12">
        <div class="card cardLogin">
          <div class="card-body text-center">
            <div class="mb-4">
              <img src="/assets/img/illustrations/email-verification.svg" alt="Email Verification" style="max-width: 200px;">
            </div>
            
            <h4 class="mb-3">Vérifiez votre adresse email</h4>
            
            <div v-if="message" class="alert alert-success alert-dismissible mb-4" role="alert">
              {{ message }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            
            <p class="mb-4">
              Nous avons envoyé un lien de vérification à votre adresse email. Veuillez vérifier votre boîte de réception et cliquer sur le lien pour vérifier votre compte.
            </p>
            
            <p class="mb-4">
              Si vous n'avez pas reçu l'email, vous pouvez demander un nouvel email de vérification.
            </p>
            
            <button @click="resendVerificationEmail" class="btn btn-primary mb-3" :disabled="loading">
              <span v-if="loading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
              Renvoyer l'email de vérification
            </button>
            
            <div>
              <button @click="logout" class="btn btn-outline-secondary">Se déconnecter</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'VerifyComponent',
  data() {
    return {
      message: '',
      loading: false
    }
  },
  mounted() {
    // Vérifier si un message de succès est présent dans l'URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('resent')) {
      this.message = 'Un nouveau lien de vérification a été envoyé à votre adresse email.';
    }
  },
  methods: {
    resendVerificationEmail() {
      this.loading = true;
      
      axios.post('/email/verification-notification')
        .then(response => {
          this.loading = false;
          this.message = 'Un nouveau lien de vérification a été envoyé à votre adresse email.';
        })
        .catch(error => {
          this.loading = false;
          if (error.response && error.response.data && error.response.data.message) {
            this.message = error.response.data.message;
          } else {
            console.error('Erreur lors de l\'envoi de l\'email de vérification:', error);
            this.message = 'Une erreur est survenue lors de l\'envoi de l\'email de vérification. Veuillez réessayer plus tard.';
          }
        });
    },
    logout() {
      // Créer un formulaire temporaire pour soumettre la requête POST
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '/logout';
      
      // Ajouter le token CSRF
      const csrfMeta = document.querySelector('meta[name="csrf-token"]');
      if (csrfMeta) {
        const csrfToken = csrfMeta.getAttribute('content');
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
      } else {
        // Si le meta tag CSRF n'est pas présent, essayer de récupérer le token depuis Laravel.csrfToken
        if (window.Laravel && window.Laravel.csrfToken) {
          const csrfInput = document.createElement('input');
          csrfInput.type = 'hidden';
          csrfInput.name = '_token';
          csrfInput.value = window.Laravel.csrfToken;
          form.appendChild(csrfInput);
        } else {
          console.error('CSRF token non trouvé. La déconnexion peut échouer.');
        }
      }
      
      // Ajouter le formulaire au document, le soumettre, puis le supprimer
      document.body.appendChild(form);
      form.submit();
      document.body.removeChild(form);
    }
  }
}
</script>

<style scoped>
/* Vous pouvez conserver les styles existants ou les adapter selon vos besoins */
</style>
